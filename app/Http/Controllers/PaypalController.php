<?php
namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\ExecutePayment;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Redirect;
use Session;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Topic;
use App\Setting;

class PaypalController extends Controller
{

  private $_api_context;
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    /** setup PayPal api context **/
    $paypal_conf = \Config::get('paypal');

    $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
    
    $this->_api_context->setConfig($paypal_conf['settings']);
  }

  public function paypal_post(Request $request)
  {
    // $validator = Validator::make($request->all(), [
    //   'topic_id' => 'required',
    // ]);
  
    // if ($validator->fails()) {
    //   return redirect('payment/add-funds/paypal')
    //     ->withErrors($validator)
    //     ->withInput();
    // }

    $topic = Topic::findorfail($request->input('topic_id'));
    $setting = Setting::whereId(1)->first();
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
  
    $item = new Item();
    $item->setName('Amount to Add')// item name
      ->setCurrency($setting->currency_code)
      ->setQuantity(1)
      ->setPrice($topic->amount); // unit price
    
    // add item to list
    $item_list = new ItemList();
    $item_list->setItems([$item]);
    
    $amount = new Amount();
    $amount->setCurrency($setting->currency_code)
      ->setTotal($topic->amount);
    
    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($item_list)
      ->setDescription('Amount to Add');
    
    $redirect_urls = new RedirectUrls();
    // Specify return & cancel URL
    $redirect_urls->setReturnUrl(route('paypal_success'))
      ->setCancelUrl(route('paypal_cancel'));
  
    $payment = new Payment();
    $payment->setIntent('Sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirect_urls)
        ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
    try {
        $payment->create($this->_api_context);
    } catch (\Exception $ex) {
      //dd($ex->getData()) ;
        // if (\Config::get('app.debug')) {

        //     return back()->with('deleted', $ex->getMessage());
            
        // } else { 
          // flash($ex->getData())->error()->important();
            return back()->with('deleted', $ex->getMessage());
            /** die('Some error occur, sorry for inconvenient'); **/
        // }
    }
  
    foreach ($payment->getLinks() as $link) {
      if ($link->getRel() == 'approval_url') {
        $redirect_url = $link->getHref();
        break;
      }
    }
    // add payment ID to session
    Session::put('paypal_payment_id', $payment->getId());
    Session::put('topic', $topic);
    Session::put('wemail', $setting->w_email);
    
    if (isset($redirect_url)) {
      // redirect to paypal
      return redirect($redirect_url);
    }
    
    Session::flash('alert', 'Unknown error occurred');
    Session::flash('alertClass', 'danger no-auto-close');
    return back();
  }

  // Paypal process payment after it is done
  public function paypal_success(Request $request)
  {

    // return Session::get('paypal_payment_id');
    // Get the payment ID before session clear
    $payment_id = Session::get('paypal_payment_id');
    $topic = Session::get('topic');
    $com_email = Session::get('wemail');

    $auth = Auth::user(); 
    $user_email = $auth->email;

    Session::put('user_email', $user_email);
    Session::put('com_email', $com_email);

    // clear the session payment ID
    Session::forget(['paypal_payment_id','topic','wemail']);  

    if (empty($request->input('PayerID')) || empty($request->input('token'))) {
      flash('Payment Failed')->error()->important();
      return redirect('/')->with('deleted', 'Payment failed');;
    }
    
    $payment = Payment::get($payment_id, $this->_api_context);
    /** PaymentExecution object includes information necessary **/
    /** to execute a PayPal account payment. **/
    /** The payer_id is added to the request query parameters **/
    /** when the user is redirected from paypal back to your site **/
    $execution = new PaymentExecution();
    $execution->setPayerId(request()->get('PayerID'));
    /**Execute the payment **/
    $result = $payment->execute($execution, $this->_api_context);

    /** dd($result);exit; /** DEBUG RESULT, remove it later **/
    if ($result->getState() == 'approved') { 
        /** it's all right **/
        /** Here Write your database logic like that insert record or value in database if you want **/
        $paypal_data = $auth->topic()->attach($topic->id, ['amount' => $topic->amount, 'transaction_id' => $payment_id, 'status' => 1]);

        if ($paypal_data) {
          if(env('MAIL_FROM_ADDRESS' != NULL) && env('MAIL_FROM_NAME') && env('MAIL_DRIVER') && env('MAIL_HOST') && env('MAIL_PORT'))
          {
            Mail::send('email.invoice', ['paypal' => $paypal_data, 'topic' => $topic], function($message) {
                $message->to(Session::get('user_email'))->from(Session::get('com_email'))->subject('Invoice');
            });
          }

            Session::forget('user_email');
            Session::forget('com_email');
        }  
        
        
        return redirect('/')->with('added', 'Payment successfully done');
    }
    
    Session::flash("deleted","Payment failed");
    return redirect('/')->with('deleted', 'Payment failed');
    
  }

  public function paypal_cancel()
  {
     flash('Payment Cancelled')->error()->important();
     return redirect('/')->with('deleted', 'Payment Cancelled');;
  }
}







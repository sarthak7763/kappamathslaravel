<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Question;

class QuestionCheckRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //

        $questionvalue=htmlentities($value);
        $checkquestion=Question::where('question',$questionvalue)->get()->first();
        if($checkquestion)
        {
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Question already exists.';
    }
}

<template>
  <div class="main-questions">
    <div class="myQuestion" v-for="(question, index) in questions">
      <div class="row">
        <div class="col-md-6">
          <blockquote>
            Total Questions &nbsp;&nbsp;{{ index+1 }} / {{questions.length}}
          </blockquote>
          <h2 class="question">Q. &nbsp;{{question.question}}</h2>

          <div class="row" v-if="question.code_snippet !== null">
            <div class="col-md-10">
              <pre class="code">
                {{question.code_snippet}}
              </pre>
            </div>
          </div>

          <div class="row" v-if="question.answer_exp !== null">
            <div class="col-md-10">
                <blockquote>
                    <span style="font-size:14px;">Answer Explanation</span>
                    <br>
                    {{ question.answer_exp }}
                </blockquote>
            </div>
          </div>

          <form class="myForm" action="/quiz_start" v-on:submit.prevent="createQuestion(question.id, question.answer, auth.id, question.topic_id)" method="post">
            <input required="" class="radioBtn" v-bind:id="'radio'+ index" type="radio" v-model="result.user_answer" value="A" aria-checked="false"> <span>{{question.a}}</span><br>
            <input required="" class="radioBtn" v-bind:id="'radio'+ index+1" type="radio" v-model="result.user_answer" value="B" aria-checked="false"> <span>{{question.b}}</span><br>
            <input required="" class="radioBtn" v-bind:id="'radio'+ index+2" type="radio" v-model="result.user_answer" value="C" aria-checked="false"> <span>{{question.c}}</span><br>
            <input required="" class="radioBtn" v-bind:id="'radio'+ index+3" type="radio" v-model="result.user_answer" value="D" aria-checked="false"> <span>{{question.d}}</span><br>
            <div v-if="question.e != null">
             <input required="" class="radioBtn" v-bind:id="'radio'+ index+4" type="radio" v-model="result.user_answer" value="E" aria-checked="false"> <span>{{question.e}}</span><br>
             </div>

              <div v-if="question.f != null">
                <input class="radioBtn" v-bind:id="'radio'+ index+5" type="radio" v-model="result.user_answer" value="F" aria-checked="false"> <span>{{question.f}}</span><br>
              </div>
              
            <div class="row">
              <div class="col-md-6 col-xs-8">
                <button type="submit" class="btn btn-wave btn-block nextbtn">Next</button>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-6">
          <div class="question-block-tabs" v-if="question.question_img != null || question.question_video_link != null || question.question_audio != null">
            <ul class="nav nav-tabs tabs-left">
              <li v-if="question.question_img != null" class="active"><a :href="'#image'+(index+1)" data-toggle="tab">Question Image</a></li>
              <li v-if="question.question_video_link != null"><a :href="'#video'+(index+1)" data-toggle="tab">Question Video</a></li>
               <li v-if="question.question_audio != null"><a :href="'#audio'+(index+1)" data-toggle="tab">Question Audio</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active" :id="'image'+(index+1)" v-if="question.question_img != null">
                <div class="question-img-block">
                  <img :src="'../images/questions/'+question.question_img" class="img-responsive" alt="question-image">
                </div>
              </div>
              <div class="tab-pane fade" :id="'video'+(index+1)" v-if="question.question_video_link != null">
                <div class="question-video-block">
                  <h3 class="question-block-heading">Question Video</h3>   
                  <iframe :id="'yui'+(index+1)" width="460" height="345" :src="question.question_video_link" frameborder="0" allowfullscreen></iframe>
                </div>
              </div>
              <div class="tab-pane fade" :id="'audio'+(index+1)" v-if="question.question_audio != null">
                <div class="question-video-block">
                  <h3 class="question-block-heading">Question Audio</h3>   
                  <audio controls :id='`xyz${index+1}`' width="460" height="345"  >
                    <source :src="question.question_audio" type="audio/mp3">
                  </audio>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {

  props: ['topic_id'],

  data () {
    return {
      questions: [],
      answers: [],
      result: {
        question_id: '',
        answer: '',
        user_id: '',
        user_answer: 0,
        topic_id: '',
      },
      auth: [],
    }
  },

  created () {
    this.fetchQuestions();
  },

  methods: {

    fetchQuestions() {
      this.$http.get(`${this.$props.topic_id}/quiz/${this.$props.topic_id}`).then(response => {
        this.questions = response.data.questions;
        this.auth = response.data.auth;
      }).catch((e) => {
        console.log(e)
      });
    },

    createQuestion(id, ans, user_id, topic_id) {
      this.result.question_id = id;
      this.result.answer = ans;
      this.result.user_id = user_id;
      this.result.topic_id = this.$props.topic_id;
      this.$http.post(`${this.$props.topic_id}/quiz`, this.result).then((response) => {
        console.log('request completed');
      }).catch((e) => {
        console.log(e);
      });
      this.result.user_answer = 0;
      this.result.topic_id = '';
    }
  }
}
</script>
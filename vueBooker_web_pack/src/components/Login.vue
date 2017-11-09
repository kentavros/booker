<template>
  <div class="login">
    <div class="loginForm text-center center-block">
      <p class="alert-danger">{{errorMsg}}</p>
      <fieldset>
        <div id="legend">
            <legend class="title">Log in to the application</legend>
        </div>
        <div class="control-group">
          <!-- Login -->
          <label class="control-label"  for="login">Login</label>
          <div class="controls">
              <input v-model="login" type="text" class="input-xlarge">
              <p class="help-block"></p>
          </div>
        </div>
        <div class="control-group">
          <!-- Password -->
          <label class="control-label"  for="pass">Password</label>
          <div class="controls">
              <input v-model="pass" type="password" class="input-xlarge">
              <p class="help-block"></p>
          </div>
        </div>
        <div class="control-group">
          <!-- Button -->
          <div class="controls">
              <button v-on:click="getLogin()" class="btn btn-success">Submit</button>
          </div>
        </div>
      </fieldset>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'loginForm',
  data () {
    return {
      msg: '',
      errorMsg: '',
      login: '',
      pass: ''
    }
  },
  methods: {
    getLogin: function(){
      var self = this
      self.errorMsg = ''
      if (self.login && self.pass)
        {
          axios.put(getUrl() + 'users/', {
            login: self.login,
            pass: self.pass
          }, axConf)
            .then(function (response) {
            if (response.data.id && response.data.hash)
            {
              self.$parent.user.id = response.data.id
              self.$parent.user.hash = response.data.hash 
              localStorage['user'] = JSON.stringify(self.$parent.user)
              self.$parent.user.login = response.data.login
              self.$parent.user.role = response.data.role
              self.$parent.user.userName = response.data.username
              self.$parent.checkUser=1
            }
            else {
              self.errorMsg = response.data
              if (self.errorMsg == 'Nothing found')
              {
                self.errorMsg = 'Error, check password and login'
              }
            }
          })
          .catch(function (error) {
            console.log(error)
          })
        }
        else
        {
          self.errorMsg = 'Enter data in all fields!'
        }
    }
  }
}
</script>

<style scoped>
.login{
  text-align: -webkit-center;
  height: 1341px;
  background-image: url(/static/img/login_or.jpg);
  background-size: cover;
}
.loginForm{
  font-size: 18px;
  position: relative;
  top: 250px;
  color: darkblue;
  width: 550px;
  background-color: rgba(255, 255, 255, 0.8);
  padding-bottom: 10px;
}
.title{
  font-size: 28px;
  font-weight: bold;
  color:darkcyan;
}
.alert-danger{
    text-align: center
}
</style>

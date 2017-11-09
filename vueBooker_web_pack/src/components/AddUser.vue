<template>
  <div class="addUser">
    <div class="regForm">
      <p class="alert-danger">{{errorMsg}}</p>
      
        <fieldset>
            <div id="legend">
            <legend class="title">User Registration</legend>
            </div>
            <div class="control-group">
            <!-- UserName -->
            <label class="control-label"  for="userName">User Name</label>
            <div class="controls">
                <input v-model="userName" type="text" id="userName" name="userName" class="input-xlarge">
                <p class="help-block">Input your name or full name</p>
            </div>
            </div>
            <div class="control-group">
            <!-- login -->
            <label class="control-label"  for="login">Login</label>
            <div class="controls">
                <input v-model="login" type="text" id="login" name="login" class="input-xlarge">
                <p class="help-block">Login can contain any letters or numbers, without spaces and latin letters </p>
            </div>
            </div>
            <div class="control-group">
            <!-- Email -->
            <label class="control-label"  for="email">Email</label>
            <div class="controls">
                <input v-model="email" type="email" id="email" name="email" class="input-xlarge">
                <p class="help-block">Input your email</p>
            </div>
            </div>
        
            <div class="control-group">
            <!-- Password-->
            <label class="control-label" for="password">Password</label>
            <div class="controls">
                <input v-model="pass" type="password" id="password" name="password" class="input-xlarge">
                <p class="help-block">Password should be at least 4 characters</p>
            </div>
            </div>
        
            <div class="control-group">
            <!-- Password -->
            <label class="control-label"  for="password_confirm">Password (Confirm)</label>
            <div class="controls">
                <input v-model="passConf" type="password" id="password_confirm" name="password_confirm" class="input-xlarge">
                <p class="help-block">Please confirm password</p>
            </div>
            </div>
            <div class="control-group">
            <!-- Role -->
            <label class="control-label"  for="role">Role</label>
            <div class="controls">
                <select class="test" v-model="role">
                    <option value="1">User</option>
                    <option class="alert-danger" value="2">Admin</option>
                </select>
                <p class="help-block">Specify the role of the user you are creating: "user" or "admin"</p>
            </div>
            </div>
        
            <div class="control-group">
            <!-- Button -->
              <div class="controls">
                <button v-if="success == ''" v-on:click="addUser()" class="btn btn-success">Register</button>
                <p v-else class="alert-info">User {{userName}}, Added</p>
                <router-link to="/emplist">
                  <button class="btn btn-info" v-on:click="$parent.getUsersList()">Back</button>
                </router-link>
              </div>
            </div>
        </fieldset>
  </div>

  </div>
</template>

<script>
var checkEmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
import axios from 'axios'
export default {
  name: 'addUserForm',
  data () {
    return {
      msg: '',
      errorMsg: '',
      userName: '',
      login: '',
      email: '',
      pass: '',
      passConf: '',
      role: '1',
      success: ''
    }
  },
  methods: {
    addUser: function(){
      var self = this
      self.errorMsg = ''
      if (self.userName && self.login && self.email && self.pass && self.passConf)
      {
        if (self.userName <=2)
        {
          self.errorMsg = 'User Name should be at least 3 characters!'
          return false
        }
        if (self.login.length <= 2)
        {
          self.errorMsg = 'Login should be at least 3 characters!'
          return false
        }
        if (!self.isValidEmail())
        {
          self.errorMsg = 'Wrong Email format!'
          return false
        }
        if (self.pass.length <= 3)
        {
          self.errorMsg = 'Password should be at least 4 characters'
          return false
        }
        else if(self.pass != self.passConf)
        {
          self.errorMsg = 'Password fields do not match'
          return false
        }
        var data = new FormData()
        data.append('hash', self.$parent.user.hash)
        data.append('id_user', self.$parent.user.id)
        data.append('username', self.userName)
        data.append('login', self.login)
        data.append('id_role', self.role)
        data.append('email', self.email)
        data.append('pass', self.pass)
        axios.post(getUrl() + 'users/', data, axConf)
            .then(function (response) {
            if (response.data === 1)
            {
                self.success = 'success'
            }
            else
            {
                self.errorMsg = response.data
            }
        })
            .catch(function (error) {
            console.log(error);
        })

      }
      else
      {
        self.errorMsg = 'Fild(s) cannot be empty!'
      }
    },
    isValidEmail: function(){
      var self = this
      if (checkEmail.test(self.email)){
        return true
      }
      else
      {
        return false
      }
    }
  }
}
</script>


<style scoped>
.addUser{
  height: 1536px;
  background-image: url(/static/img/regist.jpg);
  background-size: cover;
}
.regForm{
  position: relative;
  top: 100px;
  text-align: center;
  margin: auto;
  color: darkblue;
  width: 550px;
  background-color: rgba(255, 255, 255, 0.8);
  padding-bottom: 10px;
}
.title{
  color:darkcyan;
}
.help-block{
  color: black;
}
.test{
  
}
</style>

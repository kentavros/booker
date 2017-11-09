<template>
  <div class="editUser">
  <div class="regForm">
      <p v-if="errorMsg != ''" class="alert alert-danger">{{errorMsg}}</p>
        <fieldset>
            <div id="legend">
            <legend class="title">Edit User Information</legend>
            </div>
            <div class="control-group">
            <!-- login -->
            <label class="control-label"  for="login">Login</label>
            <div class="controls">
                <input v-model="selUser.login" style="text-align: center" type="text" id="login" name="login" placeholder="" class="input-xlarge" disabled>
                <p class="help-block">You can't change a login</p>
            </div>
            </div>
            <div class="control-group">
            <!-- Username -->
            <label class="control-label"  for="userName">User Name</label>
            <div class="controls">
                <input v-model="selUser.username" type="text" id="userName" name="userName" class="input-xlarge">
                <p class="help-block">Input your real first name</p>
            </div>
            </div>
            <div class="control-group">
              <!-- Email -->
              <label class="control-label"  for="email">Email</label>
              <div class="controls">
                  <input v-model="selUser.email" type="email" id="email" name="email" class="input-xlarge">
                  <p class="help-block">Input your email</p>
              </div>
            </div>
            <div class="control-group">
            <!-- Role -->
            <label class="control-label"  for="role">Role</label>
            <div class="controls">
                <select v-model="selUser.id_role">
                    <option value="1">User</option>
                    <option class="alert-danger" value="2">Admin</option>
                </select>
                <p class="help-block">Specify the role of the user you are creating: "user" or "admin"</p>
            </div>
            </div>
            <div class="control-group">
            <!-- Password-->
            <label class="control-label" for="password">Password</label>
            <div class="controls">
                <input v-model="pass" type="password" id="password" name="password" placeholder="You can leave it blank!" class="input-xlarge">
                <p class="help-block">You can leave it blank! If necessary, change the password - Password should be at least 4 characters</p>
            </div>
            </div>
        
            <div class="control-group">
            <!-- Password -->
            <label class="control-label"  for="password_confirm">Password (Confirm)</label>
            <div class="controls">
                <input v-model="passConf" type="password" id="password_confirm" name="password_confirm" placeholder="You can leave it blank!" class="input-xlarge">
                <p class="help-block">Please confirm password - Password should be at least 4 characters</p>
            </div>
            </div>
        
            <div v-if="success === 'success'" class="success">
              <h4 class="alert alert-info">User <strong>"{{selUser.username}}"</strong> edit success!</h4>
            </div>
            <div class="control-group">
            <!-- Button -->
            <div v-if="success == ''" class="controls">
                <button v-on:click="editUserFun()" class="btn btn-success">Edit Client</button>
                <button v-on:click="$parent.getUsersList()" class="btn btn-info">Cancel</button>
            </div>
            <div v-else class="controls"> 
                <button v-on:click="$parent.getUsersList()" class="btn btn-info">Back</button>
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
  name: 'editUserForm',
  props: ['editUser'],
  data () {
    return {
      errorMsg: '',
      selUser: {},
      pass: '',
      passConf: '',
      success: ''
    }
  },
  methods:{
    getSelUser: function(){
      var self = this
      self.selUser = self.editUser
    },
    isValidEmail: function(email){
      var self = this
      if (checkEmail.test(email)){
        return true
      }
      else
      {
        return false
      }
    },
    editUserFun: function(){
      var self = this
      self.errorMsg = ''
      if (self.selUser.username && self.selUser.email)
      {
        if (self.selUser.username <=2)
        {
          self.errorMsg = 'User Name should be at least 3 characters!'
          return false
        }
        if (!self.isValidEmail(self.selUser.email))
        {
          self.errorMsg = 'Wrong Email format!'
          return false
        }
        var data = {}
        data.hash = self.$parent.user.hash
        data.id_user = self.$parent.user.id
        data.id = self.selUser.id
        data.username = self.selUser.username
        data.role = self.selUser.id_role
        data.email = self.selUser.email
        if (self.pass != '')
        {
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
          else
          {
              data.pass = self.pass
          }
        }
        axios.put(getUrl() + 'users/', data, axConf)
          .then(function (response) {
          if (response.data === 1)
          {
              self.success = 'success'
          }
          else if (response.data === 0)
          {
              self.errorMsg = 'You have not made any changes!'
          }
          else
          {
              self.errorMsg = response.data
              if (self.errorMsg === false)
              {
                self.errorMsg = 'This email "' + self.selUser.email + '" already exists!'
              }
          }
        })
          .catch(function (error) {
          console.log(error)
        })
      }
      else{
        self.errorMsg =  'Enter data in all fields!'
      }
    }
  },
  created(){
    this.getSelUser()
  }
}
</script>

<style scoped>
.editUser{
  text-align: center;
  height: 770px;
  background-image: url(/static/img/edit_user.jpg);
  background-size: cover;
}
.regForm{
  position: relative;
  top: 30px;
  margin: auto;
  color: darkblue;
  width: 550px;
  background-color: rgba(255, 255, 255, 0.9);
  padding-bottom: 10px;
}
.title{
  color:darkcyan;
}
.help-block{
  color: black;
}
.success{
    color: darkblue;
    text-align: center;
}
.alert-danger{
    text-align: center
}
</style>

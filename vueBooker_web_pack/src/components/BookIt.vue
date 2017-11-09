<template>
  <div class="bookIt">
    <router-link class="toMain" to="/"><button class="btn btn-info" v-on:click="saveRoom()">To main</button></router-link>
    <div class="title1">
      Boardroom Booker
    </div>
    <br>
    <div class="form">
      <p v-if="errorMsg != ''" class="alert alert-danger" style="text-align: center;">{{errorMsg}}</p>
      <fieldset class="fields">
          <div class="nameRoom">
              {{room.name}}
          </div>
            <div class="control-group">
              <!-- User selected -->
              <div class="controls">
                  <p class="help-block">1. Booked for:</p>
                  <select v-model="bookedUser">
                      <option v-for="user in usersList" :value="user.id">{{user.username}}</option>
                  </select>
              </div>
            </div>
            <div class="control-group">
              <!-- Meeting -->
              <div class="controls">
                  <p class="help-block">2. I would like to book the meeting (on weekends can not assign an event):</p>
                  <select v-model="selMonth">
                      <option v-for="mon in nameMonth" :value="mon.id">{{mon.name}}</option>
                  </select>
                  <select v-model="selDay">
                      <option v-for="day in days" v-if="day.flag == 'yes'" :value="day.id">{{day.id}}</option>
                      <option  v-else class="alert-danger" :value="day.id" disabled>{{day.id}}</option>
                  </select>
                  <select v-model="selYear">
                      <option v-for="year in years" :value="year">{{year}}</option>
                  </select>
              </div>
            </div>

            <div v-if="selTimeFormat == '12-24'" class="control-group">
              <!-- Time Meeting 12-24 Format-->
              <div class="controls">
                  <p class="help-block">3. Specify what the time and end of the meeting:</p>
                  Start:
                  <select v-model="selTimeH_Start">
                      <option v-for="tH_s in timeH_Start" :value="tH_s">{{tH_s}}</option>
                  </select>
                  <select v-model="selTimeM_Start">
                      <option v-for="m_s in minStart" :value="m_s">{{m_s}}</option>
                  </select>
              </div>
              <br>
              <div class="controls">
                End:
                  <select v-model="selTimeH_End">
                      <option v-for="tH_e in timeH_End" :value="tH_e">{{tH_e}}</option>
                  </select>
                  <select v-model="selTimeM_End">
                      <option v-for="m_e in minEnd" :value="m_e">{{m_e}}</option>
                  </select>
              </div>
              <br>
              <p>
              <button v-on:click="getTimeFormat('am-pm')">set AM / PM</button>
              </p>
            </div>

            <div v-else class="control-group">
              <!-- Time Meeting AM - PM Fromat-->
              <div class="controls">
                  <p class="help-block">3. Specify what the time and end of the meeting:</p>
                  Start:
                  <select v-model="selTimeH_Start">
                      <option v-for="tH_s in timeH_Start" :value="tH_s.val">{{tH_s.title}}</option>
                  </select>
                  <select v-model="selTimeM_Start">
                      <option v-for="m_s in minStart" :value="m_s">{{m_s}}</option>
                  </select>
                  <select v-model="selAmPmStart">
                      <option value="am">AM</option>
                      <option value="pm">PM</option>
                  </select>
              </div>
              <br>
              <div class="controls">
                End:
                  <select v-model="selTimeH_End">
                      <option v-for="tH_e in timeH_End" :value="tH_e.val">{{tH_e.title}}</option>
                  </select>
                  <select v-model="selTimeM_End">
                      <option v-for="m_e in minEnd" :value="m_e">{{m_e}}</option>
                  </select>
                  <select v-model="selAmPmEnd">
                      <option value="am">AM</option>
                      <option value="pm">PM</option>
                  </select>
              </div>
              <br>
              <p>
              <button v-on:click="getTimeFormat('12-24')">set 12 / 24</button>
              </p>
            </div>
            <div class="control-group">
              <!-- Description Meeting -->
              <div class="controls">
                  <p class="help-block">4. Enter the specifics for the meeting:</p>
                  <textarea v-model="description" rows="4" cols="25" name="description" style="width:500px;">
                  </textarea>
              </div>
            </div>
            <div class="control-group">
              <!-- Recurring Meeting -->
              <div class="controls">
                <p class="help-block">5. Is this going to be a recurring event?</p>
                <input type="radio" id="no" value="no" v-model="recurring">
                <label for="no">no</label>
                <br>
                <input type="radio" id="yes" value="yes"  v-model="recurring">
                <label for="yes">yes</label>
              </div>
            </div>
  
            <div v-if="recurring == 'yes'" class="control-group">
              <!-- Periodicity Meeting -->
              <div class="controls">
                <p class="help-block">6. If it is recurring, specify weekly, bi-weekly, or monthly.</p>
                <input v-on:change="selDuration=1" type="radio" id="weekly" value="weekly" v-model="recurringMethod">
                <label for="weekly">weekly</label>
                <br>
                <input v-on:change="selDuration=1" type="radio" id="bi-weekly" value="bi-weekly" v-model="recurringMethod">
                <label for="bi-weekly">bi-weekly</label>
                <br>
                <input v-on:change="selDuration=1" type="radio" id="monthly" value="monthly" v-model="recurringMethod">
                <label for="monthly">monthly</label>
                <p class="help-block">If weekly or bi-weekly, specify the number of weeks for it to keep recurring.
                  If monthly, specify the number of months. (If you choose "bi-weekly" and put in an odd number of weeks, the computer will round down.)
                </p>
                <br>
                <input  v-if="recurringMethod != 'monthly'" v-model="selDuration" v-on:input="setFilterCount(selDuration)" type="text" id="numPeriod" class="input-xlarge" style="width: 30px;">
                <input v-else disabled v-model="selDuration" type="text" id="numPeriod" class="input-xlarge" style="width: 30px;">
                <label for="numPeriod">Duration (max {{setCountEvent(recurringMethod)}})</label>
              </div>
            </div>
            <div class="buttons control-group">
            <!-- Button -->
            <div class="controls">
            <p class="alert-info" style="text-align: center;">{{msg}}</p>
            <p v-if="errorMsg != ''" class="alert alert-danger" style="text-align: center;">{{errorMsg}}</p>
            <div v-if="success != '1'">
              <button v-on:click="addEvent()" class="btn btn-success">Submit</button>
              <router-link to="/"><button class="btn btn-info" v-on:click="saveRoom()">Back</button></router-link>
            </div>
            <div v-else>
              <router-link to="/"><button class="btn btn-info" v-on:click="saveRoom()">Back</button></router-link>
            </div>
            </div>
            </div>
      </fieldset>
    </div>
    </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'BookItForm',
  data () {
    return {
      msg: '',
      errorMsg: '',
      id: '',
      selTimeFormat: '',
      room: {},
      user: {},
      users: [],
      date: new Date(),
      bookedUser: '',
      currentDay: '',
      currentMonth: '',
      currentYear: '',
      selDay: '',
      selMonth: '',
      selYear: '',
      selAmPmStart: 'am',
      selAmPmEnd: 'am',
      selTimeH_Start: timeStart,
      selTimeM_Start: '',
      selTimeH_End: '',
      selTimeM_End: '',
      description: '',
      recurring: 'no',
      recurringMethod: 'weekly',
      selDuration: '1',
      success: ''
    }
  },
  methods:{
    getTimeFormat: function(param)
    {
      var self = this
      self.selTimeFormat = param
      if (param == 'am-pm')
      {
        self.selTimeH_Start = timeStart
        self.selTimeM_Start = min00
        self.selTimeH_End = timeStart
        self.selTimeM_End =min30
        self.selAmPmStart = 'am'
        self.selAmPmEnd = 'am'
      }
    },

    saveRoom: function()
    {
      var self = this
      localStorage['room'] = JSON.stringify(self.room)
    },

    addEvent: function()
    {
      var self = this
      self.errorMsg = ''
      self.msg = ''
      var data = new FormData()
      var date = new Date()
      if (!self.description || self.description.length < 6){
        self.errorMsg = 'The description field can not be empty and length of the description can not be shorter than 6 characters!'
        return false
      }
      if(!self.findWeekday(self.selDay)){
        self.errorMsg = 'At the weekend the Boardroom is closed - choose another date!'
        return false
      }
        var dateTimeStart = new Date(self.selYear, self.selMonth, self.selDay, self.selTimeH_Start, self.selTimeM_Start)
        if (date.getTime() > dateTimeStart.getTime())
        {
          self.errorMsg = 'Can not add event! Incorrect start time specified!'
          return false
        }
        dateTimeStart = dateTimeStart.getTime()
        var dateTimeEnd = new Date(self.selYear, self.selMonth, self.selDay, self.selTimeH_End, self.selTimeM_End)
        dateTimeEnd = dateTimeEnd.getTime()
        data.append('hash', self.user.hash)
        data.append('id_user', self.user.id)
        data.append('booked_for', self.bookedUser)
        data.append('id_room', self.room.id)
        data.append('dateTimeStart', dateTimeStart)
        data.append('dateTimeEnd', dateTimeEnd)
        data.append('description', self.description)
        if (self.recurring == 'yes'){
          data.append('recurringMethod', self.recurringMethod)
          data.append('duration', self.selDuration)
        }
        axios.post(getUrl() + 'events/', data, axConf)
          .then(function (response) {
              if (response.data === 1 || response.data == true)
              {
                  self.msg = 'The event '+ self.selTimeH_Start +':'+ self.selTimeM_Start + '-'+
                    self.selTimeH_End + ':' + self.selTimeM_End +' - has been added successfully! The text for this event is "'+
                    self.description + '"'
                  self.success = '1'
              }
              else if (Array.isArray(response.data))
              {
                var i = 1
                  response.data.forEach(function(event){
                    self.errorMsg += i + ') ' + event +'. '
                    i++
                  })
                  self.msg = 'Your events - added with Errors!'
              }
              else
              {
                  self.errorMsg = response.data
              }
          })
              .catch(function (error) {
              console.log(error);
          })
    },
    findWeekday: function(selDay){
      var self = this
      var count = 0
      self.days.forEach(function(day){
        if (day.id == selDay)
        {
          if (day.flag == 'yes'){
            count++
          }
        }
      })
      if (count == 1)
      {
        return true
      }
      else{
        return false
      }
    },
    setFilterCount: function(count)
    {
      var self = this
      count = Math.trunc(+count)
      if (!count || count < 0){
        self.selDuration=1
        return true
      }
      if (self.recurringMethod == 'weekly')
      {
        if (count > 4){
          self.selDuration=4
          return true
        }
      }
      if (self.recurringMethod == 'bi-weekly')
      {
        if (count > 2){
          self.selDuration=2
          return true
        }
      }
      if (self.recurringMethod == 'monthly')
      {
          self.selDuration=1
          return true
      }
    },
    getDayMonthYear: function()
    {
      var self = this
      self.currentDay = self.date.getDate()
      self.currentMonth =self.date.getMonth()
      self.currentYear = self.date.getFullYear()
      self.selDay = self.currentDay
      self.selMonth = self.currentMonth
      self.selYear = self.currentYear
    },
    getRoom: function(id){
      var self = this
      axios.get(getUrl() + 'rooms/hash/' + self.user.hash + '/id_user/' + self.user.id +
      '/id/' + id, true)
            .then(function (response) {
            if (Array.isArray(response.data))
            {
              self.room = response.data[0]
            }
            else{
              self.errorMsg = response.data
            }
        })
        .catch(function (error) {
          console.log(error)
        })
    },
    checkUserFun: function(){
      var self = this
      if (localStorage['user'])
      {
        self.user = JSON.parse(localStorage['user'])
        self.bookedUser = self.user.id
        axios.get(getUrl() + 'users/hash/' + self.user.hash + '/id_user/' + self.user.id +
        '/id/' + self.user.id, true)
            .then(function (response) {
              if (Array.isArray(response.data))
              {
                if (self.user.hash === response.data[0].hash)
                {
                  self.user = response.data[0]
                  self.getUsers()
                }
              }
              else 
              {
                self.$router.push('/')
              }
            })
            .catch(function (error) {
              console.log(error)
            });
      }
      else{
        self.$router.push('/')
      }
    },
    getUsers:function(){
      var self = this
      if (self.user.role != 'admin')
      {
        return false
      }
      axios.get(getUrl() + 'users/hash/' + self.user.hash + '/id_user/' + self.user.id)
          .then(function (response) {
          if (Array.isArray(response.data))
          {
            self.users = response.data
          }
          else{
            self.errorMsg = response.data
          }
      })
      .catch(function (error) {
        console.log(error)
      })

    },
    setCountEvent:function(str){
      if (str == 'weekly')
      {
        self.selDuration = 4
        return '4 weeks'
      }
      if (str == 'bi-weekly')
      {
        self.selDuration = 2
        return '2 bi-weeks'
      }
      if (str == 'monthly')
      {
        self.selDuration = 1
        return '1 month'
      }
    }
  },
  created(){
    this.getDayMonthYear()
    this.checkUserFun()
    this.id = this.$route.params.id
    this.getTimeFormat(this.selTimeFormat = this.$route.params.time)
    this.getRoom(this.id)
  },
  computed: {
    usersList(){
      var self = this 
      if (self.users.length != 0)
      {
        return self.users
      }
      else
      {
        var arr = []
        arr.push(self.user)
        return arr
      }
    },
    timeH_Start(){
      var self = this
      var timeH = []
      if (self.selTimeFormat == 'am-pm')
      {
        if (self.selAmPmStart == 'am'){
          for(var i=timeStart; i<timeNoon; i++){
            timeH.push({val: i, title: i})
          }
          return timeH
        }
        else if (self.selAmPmStart == 'pm'){
          for(var i=timeNoon; i<timeEnd; i++){
            var objTime = {}
            objTime.val = i
            objTime.title = (i != timeNoon) ? i-timeNoon : timeNoon
            timeH.push(objTime)
          }
          self.selTimeH_Start = timeNoon
          return timeH
        }
      }
      else
      {
        for (var i=timeStart; i<timeEnd; i++){
          timeH.push(i)
        }
        return timeH
      }
    },
    timeH_End(){
      var self = this
      var timeH = []
      if (self.selTimeFormat == 'am-pm')
      {
        if (self.selAmPmEnd == 'am'){
          for(var i=timeStart; i<timeNoon; i++){
            timeH.push({val: i, title: i})
          }
          return timeH
        }
        else if (self.selAmPmEnd == 'pm'){
          for(var i=timeNoon; i<timeEnd+1; i++){
            var objTime = {}
            objTime.val = i
            objTime.title = (i != timeNoon) ? i-timeNoon : timeNoon
            timeH.push(objTime)
          }
          self.selTimeH_End = timeNoon
          return timeH
        }
      }
      else
      {
        if (self.selTimeH_Start > self.selTimeH_End)
        {
          self.selTimeH_End = self.selTimeH_Start
        }
        for (var i=self.selTimeH_Start; i<timeEnd+1; i++){
          timeH.push(i)
        }
        return timeH
      }
    },
    minStart(){
      var self = this
      var min = []
      if (self.selTimeH_Start == self.selTimeH_End && self.selTimeM_End == min30)
      {
        self.selTimeM_Start = min00
        min.push(min00)
      }
      else
      {
        min.push(min00)
        min.push(min30)
      }
      return min
    },
    minEnd(){
      var self = this
      var min = []
      if (self.selTimeH_Start == self.selTimeH_End){
        self.selTimeM_End = min30
        min.push(min30)
      }
      else if (self.selTimeH_End == timeEnd)
      {
        self.selTimeM_End = min00
        min.push(min00)
      }
      else
      {
        min.push(min00)
        min.push(min30)
      }
      return min
    },
    days(){
      var self = this
      var days = []
      var date = new Date(self.selYear, self.selMonth)
      if (self.selYear != self.currentYear || self.selMonth != self.currentMonth)
      {
        self.selDay = 1
      }
      else if (self.selYear == self.currentYear && self.selMonth == self.currentMonth)
      {
        self.selDay = self.currentDay
        date.setDate(self.currentDay)
      }
      while(date.getMonth() == self.selMonth){
        if (date.getDay() != 0 && date.getDay() != 6)
        {
          days.push({id: date.getDate(), flag: 'yes'})
          date.setDate(date.getDate()+1)
        }
        else{
          days.push({id: date.getDate(), flag: 'no'}) 
          date.setDate(date.getDate()+1)
        }
      }
      return days
    },
    nameMonth(){
      var self = this
      var nameMon = getNameMonth('en')
      var months = []
      if (self.selYear == self.currentYear)
      {
        var date = new Date(self.selYear, self.currentMonth)
        self.selMonth = self.currentMonth
      }
      else{
        var date = new Date(self.selYear)
        self.selMonth = 0
      }
      for (var i=date.getMonth(); i<12; i++)
      {
        months.push({id: i, name: nameMon[i]})
      }
      return months
    },
    years(){
      var self = this
      var years = []
      for (var i=0; i<5; i++){
        years.push(self.currentYear+i)
      }
      return years
    }
  }
}
</script>

<style scoped>
.toMain button{
  margin-top: 15px;
}
.title1{
  font-size: 35px;
  text-align: center;
}
.nameRoom{
  width: 200px;
  text-align: center;
  font-size: 24px;
  border: 1px solid;
  margin: auto;
}
.form{
  margin: auto;
  color: darkblue;
  width: 900px;
  background-color: rgba(255, 255, 255, 0.7);
  padding-bottom: 10px;
}
.help-block{
  color: black;
  margin-top: 20px;
  margin-bottom: 20px;
}
.buttons{
  margin-top: 30px;
  margin-bottom: 40px;
}
.fields{
  padding-top: 20px;
  padding-left: 30px;
}
</style>

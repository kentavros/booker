<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container" :class="{plusWidth: timeFormat == 'am-pm'}">
          <div class="btnclose">
          <button class="btn-danger btn-xs" type="button" v-on:click="$emit('close')" >X</button>
          </div>
            <h4>B.B. DETAILS</h4>
            <p v-if="errorMsg != ''" class="alert-danger" style="text-align: center;" >{{errorMsg}}</p>
            <p v-if="msg != ''" class="alert-info" style="text-align:center">{{msg}}</p>
            <table class="table table-hover table-bordered">
              <tbody>
                <!-- 12-24 Format -->
                <tr v-if="timeFormat != 'am-pm'">
                  <th>When:</th>
                  <td v-if="(access == '2' || user.id == event.id_user) && success != 'success' && Date.now() < Date.parse(sentEvent.time_start)">
                    <select v-model="selTimeH_Start">
                      <option v-for="tH_s in timeH_S" :value="tH_s">{{tH_s}}</option>
                    </select>
                    <select v-model="selTimeM_Start">
                      <option value="0">00</option>
                      <option value="30">30</option>
                    </select>
                     - 
                    <select v-model="selTimeH_End">
                      <option v-for="tH_e in timeH_E" :value="tH_e">{{tH_e}}</option>
                    </select>
                    <select v-model="selTimeM_End">
                      <option value="0">00</option>
                      <option value="30">30</option>
                    </select>
                  </td>
                  <td v-else>
                   {{event.timeString}}
                  </td>
                </tr>

                <!-- AM-PM Format -->
                <tr v-else>
                  <th>When:</th>
                  <td v-if="(access == '2' || user.id == event.id_user) && success != 'success' && Date.now() < Date.parse(sentEvent.time_start)">
                    <select v-model="selTimeH_Start">
                      <option v-for="tAmPmS in timeAMPM_Start" :value="tAmPmS.val">{{tAmPmS.title}}</option>
                    </select>
                    <select v-model="selTimeM_Start">
                      <option value="0">00</option>
                      <option value="30">30</option>
                    </select>
                    <select v-model="selAmPmStart">
                      <option value="am">AM</option>
                      <option value="pm">PM</option>
                    </select>
                     - 
                    <select v-model="selTimeH_End">
                      <option v-for="tAmPmE in timeAMPM_End" :value="tAmPmE.val">{{tAmPmE.title}}</option>
                    </select>
                    <select v-model="selTimeM_End">
                      <option value="0">00</option>
                      <option value="30">30</option>
                    </select>
                    <select v-model="selAmPmEnd">
                        <option value="am">AM</option>
                        <option value="pm">PM</option>
                    </select>
                  </td>
                  <td v-else>
                   {{event.timeString}}
                  </td>
                </tr>
                <tr>
                  <th>Notes:</th>
                  <td v-if="(access == '2' || user.id == event.id_user) && success != 'success' && Date.now() < Date.parse(sentEvent.time_start)">
                    <input type="text" v-model="selDescription" :value="event.description">
                  </td>
                  <td v-else>{{event.description}}</td>
                </tr>
                <tr>
                  <th>Who:</th>
                  <td v-if="event.user_name && access == '2' && success != 'success' && Date.now() < Date.parse(sentEvent.time_start)">
                    <select class="selUser" v-model="selUser">
                        <option v-for="user in users" :value="user.id">{{user.username}}</option>
                    </select>
                  </td>
                  <td v-else-if="event.user_name && access == '1' && success != 'success' && Date.now() < Date.parse(sentEvent.time_start)">{{event.user_name}}</td>
                  <td v-else-if="!event.user_name" class="alert-danger">The user has been removed</td>
                  <td v-else>{{event.user_name}}</td>
                </tr>
                <tr>
                  <td colspan="2">Submitted: {{event.create_time}}</td>
                </tr>
              </tbody>
            </table>
          <div v-if="occurrenceSection == 'show' && Date.now() < Date.parse(sentEvent.time_start)" class="checkA">
              <input type="checkbox" id="checkbox" v-model="checked">
              <label for="checkbox">Apply to all occurrences?</label>
          </div>
          <div v-if="success != 'success'">
          <div v-if="(access == '2' || user.id == event.id_user) && Date.now() < Date.parse(sentEvent.time_start)" class="btn-section">
            <button class="btn btn-primary" v-on:click="updateEvent()">Update</button>
            <button class="btn btn-danger" v-on:click="deleteEvent()">Delete</button>
          </div>
          </div>
          <div v-else style="text-align:center">
            <button class="btn btn-primary" v-on:click="$emit('close')" >Close</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
<script>
import axios from 'axios'
export default {
  name: 'HelloWorld',
  props: ['sentEvent', 'sentUser', 'timeFormat'],
  data () {
    return {
      msg: '',
      errorMsg: '',
      success: '',
      access: '',
      eventParent: '',
      event: {},
      events:[],
      user: {},
      users: [],
      occurrenceSection: '',
      checked: false,
      selDescription: '',
      selUser: '',
      eventDay: '',
      eventMonth: '',
      eventYear: '',
      selTimeH_Start: '',
      selTimeM_Start: '',
      selTimeH_End: '',
      selTimeM_End: '',
      selAmPmStart: '',
      selAmPmEnd: ''
    }
  },
  methods: {
    updateEvent: function(){
      var self = this
      self.errorMsg = ''
      self.msg = ''
      var timeSH = self.selTimeH_Start
      var timeSM = self.selTimeM_Start
      var timeEH = self.selTimeH_End
      var timeEM = self.selTimeM_End
//Validate sent Data
      if (timeSH == timeEH && timeSM == timeEM){
        self.errorMsg = 'Specify the correct End date for the event!'
        return false
      }
      if (timeSH > timeEH){
        self.errorMsg = 'The end time of an event can not be earlier than the start time!'
        return false
      }
      if (timeEH == timeEnd && timeEM == min30)
      {
        self.errorMsg = 'Error! Maximum end time ' + timeEnd + ':' + min00 + '!'
        return false
      }
      var date_start = new Date(self.event.time_start)
      var date_end = new Date(self.event.time_end)
      if (timeSH == date_start.getHours() && timeSM == date_start.getMinutes()
      && timeEH == date_end.getHours() && timeEM == date_end.getMinutes()
      && self.selDescription == self.sentEvent.description && self.selUser == self.sentEvent.id_user)
      {
        self.errorMsg = 'You have not made any changes!'
        return false
      }
      if (self.selDescription.length < 6){
        self.errorMsg = 'The description field can not be empty and length of the description can not be shorter than 6 characters!'
        return false
      }
//Set params prepare to request
      var data = {}
      data.hash = self.user.hash
      data.id_user = self.user.id
//If "checked" recurring update
      if (self.checked){
        data.timestamp = date_start.getTime()
        data.checked = self.prepareDataRecurringEventsForUpdate(self.events)
      }
      else
      {
        var dateTimeStart = new Date(self.eventYear, self.eventMonth, self.eventDay, timeSH, timeSM)
        var dateTimeEnd = new Date(self.eventYear, self.eventMonth, self.eventDay, timeEH, timeEM)
        dateTimeStart = dateTimeStart.getTime()
        dateTimeEnd = dateTimeEnd.getTime()
        data.booked_for = self.selUser
        data.event_id = self.event.id
        data.dateTimeStart = dateTimeStart
        data.dateTimeEnd = dateTimeEnd
        data.id_room = self.event.id_room
        data.description = self.selDescription
      }
      var minStart = (timeSM == 0)? '00' : timeSM
      var minEnd = (timeEM ==0)? '00' : timeEM
      axios.put(getUrl() + 'events/', data, axConf)
        .then(function (response) {
          if (response.data == 1 || response.data == true)
          {
            self.msg = 'Event '+ timeSH+ ':'+ minStart + '-' + timeEH+ ':' + minEnd + ' update Successfully!'
            self.success = 'success'
            self.$emit('refresh')
          }
          else if (Array.isArray(response.data))
          {
            var i = 1
              response.data.forEach(function(event){
                self.errorMsg += i + ') ' + event +'. '
                i++
              })
              self.msg = 'Your events '+ timeSH+ ':'+ minStart + '-' + timeEH+ ':' + minEnd + ' - Updated with error(s)!'
              self.success = 'success'
              self.$emit('refresh')
          }
          else
          {
            self.errorMsg = response.data
          }
        })
          .catch(function (error) {
          console.log(error)
        })
    },

    prepareDataRecurringEventsForUpdate: function(events){
      var self = this
      var arrDataEvents = []
      events.forEach(function(el){
        var newEvent = {}
        var date_start = new Date(el.time_start)
        var date_end = new Date(el.time_end)
        var dateTimeStart = new Date(date_start.getFullYear(), date_start.getMonth(), date_start.getDate(),
        self.selTimeH_Start, self.selTimeM_Start)
        var dateTimeEnd = new Date(date_end.getFullYear(), date_end.getMonth(), date_end.getDate(),
        self.selTimeH_End, self.selTimeM_End)
        dateTimeStart = dateTimeStart.getTime()
        dateTimeEnd = dateTimeEnd.getTime()
        
        newEvent.event_id = el.id
        newEvent.booked_for = self.selUser
        newEvent.id_room = el.id_room
        newEvent.description = self.selDescription
        newEvent.dateTimeStart = dateTimeStart
        newEvent.dateTimeEnd = dateTimeEnd
        arrDataEvents.push(newEvent)
      })
      return arrDataEvents
    },

    deleteEvent: function(){
      var self = this
      self.errorMsg = ''
      self.msg = ''
      self.success = ''
      var result = confirm('Do you want to remove Event(s)?')
      if (!result)
      {
        return false
      }
      var requestUrl = ''
      if (self.checked){
        requestUrl = getUrl() + 'events/hash/' + self.user.hash + '/id_user/' + self.user.id +
      '/id/' + self.event.id + '/checked/' + self.checked + '/id_parent/' + self.event.id_parent +
      '/time_start/' + self.event.time_start + '/event_id_user/' + self.event.id_user
      }
      else
      {
        requestUrl = getUrl() + 'events/hash/' + self.user.hash + '/id_user/' + self.user.id +
      '/id/' + self.event.id
      }
      var minStart = (self.selTimeM_Start == 0)? '00' : self.selTimeM_Start
      var minEnd = (self.selTimeM_End ==0)? '00' : self.selTimeM_End
      axios.delete(requestUrl)
          .then(function (response) {
          if (response.data == 1)
          {
            self.msg = 'The event '+ self.selTimeH_Start + ':'+ minStart +
            '-'+self.selTimeH_End + ':' + minEnd +' has been removed!'
            self.success = 'success'
            self.$emit('refresh')
          }
          else if (response.data > 1)
          {
            self.msg ='The events (quantity - "'+ response.data +'") '+ self.selTimeH_Start + ':'+ minStart +
            '-'+self.selTimeH_End + ':' + minEnd +' has been removed!'
            self.success = 'success'
            self.$emit('refresh')
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
    setProporties: function(){
      var self = this
      self.user = self.sentUser
      self.event = self.sentEvent
      self.selDescription = self.sentEvent.description
      self.selUser = self.sentEvent.id_user
      self.checkUserRole()
      self.getUsersList()
      if (self.access == '2' || self.user.id == self.event.id_user)
      {
        self.getEventsIdParent()
      }
      self.getEventTime()
    },
    checkUserRole: function(){
      var self = this
      if (self.user.role == 'admin')
      {
        self.access = '2'
      }
      else{
        self.access = '1'
      }
    },
    getUsersList: function(){
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
    getEventsIdParent: function(){
      var self = this
      self.errorMsg = ''
      self.events = []
      var requestUrl = ''
      if (self.event.id_parent)
      {
        requestUrl = getUrl() + 'events/hash/' + self.user.hash + '/id_user/' + self.user.id + 
      '/flag/parent/id/' + self.event.id_parent + '/event_id_user/' + self.event.id_user
      }
      else
      {
        requestUrl = getUrl() + 'events/hash/' + self.user.hash + '/id_user/' + self.user.id + 
      '/flag/parent/id/' + self.event.id + '/event_id_user/' + self.event.id_user
      }
      axios.get(requestUrl)
        .then(function (response) {
        if (Array.isArray(response.data))
        {
          self.events = response.data 
          if (self.events.length > 1)
          {
            self.occurrenceSection = 'show'
          }
        }
        else
        {
          self.errorMsg = response.date
        }
        })
        .catch(function (error) {
          console.log(error)
        })
    },
    getEventTime: function(){
      var self = this
      var date_start = new Date(self.event.time_start)
      var date_end = new Date(self.event.time_end)
      self.eventYear = date_start.getFullYear()
      self.eventMonth = date_start.getMonth()
      self.eventDay = date_start.getDate()
      self.selTimeH_Start = date_start.getHours()
      self.selTimeM_Start = date_start.getMinutes()
      self.selTimeH_End = date_end.getHours()
      self.selTimeM_End = date_end.getMinutes()
      if (self.timeFormat == 'am-pm')
      {
        if (self.selTimeH_Start >= timeNoon){
          self.selAmPmStart = 'pm'
        }
        else
        {
          self.selAmPmStart = 'am'
        }

        if (self.selTimeH_End >= timeNoon){
          self.selAmPmEnd = 'pm'
        }
        else
        {
          self.selAmPmEnd = 'am'
        }
      }
    }
  },
  created(){
    this.setProporties()
  },
  computed: {
    timeAMPM_Start(){
      var self = this
      var timeH = []
      if (self.timeFormat == 'am-pm')
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
          return timeH
        }
      }
    },

    timeAMPM_End(){
      var self = this
      var timeH = []
      if (self.timeFormat == 'am-pm')
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
          return timeH
        }
      }
    },

    timeH_S(){
      var self = this
      var timeH = []
      for (var i=timeStart; i<timeEnd; i++){
        timeH.push(i)
      }
      return timeH
    },

    timeH_E(){
      var self = this
      var timeH = []
      for (var i=timeStart; i<timeEnd+1; i++){
        timeH.push(i)
      }
      return timeH
    }
  }
}
</script>

<style scoped>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}
.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}
.modal-container {
  width: 333px;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
}
.modal-enter {
  opacity: 0;
}
.modal-leave-active {
  opacity: 0;
}
.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
.btnclose{
  float: right;
}
.btn-section{
  text-align: center;
}
.btn-section .btn-danger{
  margin-left: 20px;
}
h4{
  text-align: center;
}
th{
  width: 50px;
}
.checkA{
  text-align: center;
  margin-bottom: 10px;
}
.plusWidth{
  width: 435px;
}
</style>

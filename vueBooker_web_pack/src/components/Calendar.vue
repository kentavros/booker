<template>
  <div class="calendar">
<!-- Modal Window for Events -->
     <modalwindow 
     v-if="showModal" 
     :sentUser="user" :sentEvent="sentEvent" :timeFormat="selTimeFormat" 
     v-on:refresh="getEventsMonth()"
      v-on:close="showModal = false">
     </modalwindow>

<!-- Calendar -->
    <p class="alert-danger" style="text-align: center;">{{errorMsg}}</p>
    <div class="row">
      <div class="shadow col-md-10">
          <p class="rooms">
            <button v-for="(room,index) in rooms" v-on:click="selRoomFun(index)" class="btnRoom btn btn-info btn-lg" 
            :class="{selBtn: room.id == selRoom.id}">
              {{room.name}}
            </button>
          </p>
          <p v-if="nameMonth == 'en'" class="roomSel">
            Your location is: <strong>{{selRoom.name}}</strong>
          </p>
          <p v-else class="roomSel">
            Вы находитесь: <strong>{{selRoom.name}}</strong>
          </p>
        <div class="title">
          <button v-on:click="minusMonth()" class="btn btn-default">&#9668;</button>
          <p>{{getMonth[currentMonth]}} {{currentYear}}</p>
          <button v-on:click="plusMonth()" class="btn btn-default">&#9658;</button>
        </div>
        <table class="table table-bordered">
          <thead>
          <tr class="info">
            <th v-for="wday in getDays">{{wday}}</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="week in weeks">
            <td class="day" v-for="day in week" :class="{date: day[0] == currentDay}">{{day[0]}}
                <p  class="events" v-if="day.length > 1" v-for="event in day[1]" >
                  <button class="btn btn-link" v-on:click="showEvent(event)" >{{event.timeString}}</button>
                  </p>
              </td>  
          </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-2">
        <div class="menu">
          <div class="right-top-menu">
            <!-- RU.EN - Calendar only -->
            <div class="ru-en-btn">
              <button v-if="nameMonth == 'en'" v-on:click="getRu()" class="btn btn-info">Ru</button>
              <button v-else-if="nameMonth == 'ru'" v-on:click="getEn()" class="btn btn-info">En</button>
            </div>
            <div class="am-pm-btn">
              <button v-if="selTimeFormat == '12-24'" v-on:click="getTimeFormat('am-pm')" class="btn btn-info">AM-PM</button>
              <button v-else-if="selTimeFormat == 'am-pm'" v-on:click="getTimeFormat('12-24')" class="btn btn-info">12-24</button>
            </div>
            <div class="mon-sun-btn">
              <button v-if="weekDays == 'sun'" v-on:click="firstMonday()" class="btn btn-info">Monday First</button>
              <button v-else-if="weekDays == 'mon'" v-on:click="firstSunday()" class="btn btn-info">Sunday First</button>
            </div>
          </div>
          <div class="btn-Book-Emp">
            <router-link :to="{ name: 'BookIt', params: { id: selRoom.id, time: selTimeFormat }}"><button class="btn btn-success">Book It!</button></router-link>
            <router-link v-if="user.role == 'admin'" to="/emplist">
              <button class="btn btn-danger">Employee List</button>
            </router-link>
          </div>
        </div>
      </div>
  </div>
  </div>
</template>

<script>
import axios from 'axios'
import Modalwindow from './Modalwindow'
import BookIt from './BookIt'
export default {
  name: 'calendar',
  props: ['user'],
  data () {
    return {
      msg: '',
      errorMsg: '',
      date: new Date(),
      weeks: [],
      currentMonth: '',
      currentYear: '',
      weekDays: 'sun',
      nameMonth: 'en',
      rooms: [],
      selRoom: {
        id: '1',
        name: ''
      },
      eventsMonth: [],
      showModal: false,
      sentEvent: {},
      selTimeFormat: '12-24'
    }
  },
  methods:{
    getTimeFormat: function(format)
    {
      var self = this
      self.selTimeFormat = format
      self.getEventsMonth()
    },

    showEvent: function(event){
      var self = this
      self.showModal = true
      self.sentEvent = event
    },

    getEventsMonth: function(){
      var self = this
      self.eventsMonth = []
      self.errorMsg = ''
      var year = self.currentYear
      var month = self.currentMonth+1
      var dateString = year + '-' + month 
      axios.get(getUrl() + 'events/hash/' + self.user.hash + '/id_user/' + self.user.id +
      '/flag/like/time_start/' + dateString)
          .then(function (response) {
          if (Array.isArray(response.data))
          {
            self.eventsMonth = response.data
            self.getArrayCalendar()
          }
          else{
            self.getArrayCalendar()
          }
      })
      .catch(function (error) {
        console.log(error)
      })
    },
    selRoomFun: function(index){
      var self = this
      self.selRoom = self.rooms[index]
      self.getEventsMonth()
    },
    getRooms: function(){
      var self = this
      axios.get(getUrl() + 'rooms/hash/' + self.user.hash + '/id_user/' + self.user.id)
          .then(function (response) {
          if (Array.isArray(response.data))
          {
            self.rooms = response.data
            if (!localStorage['room'])
            {
              self.selRoom = self.rooms[0]
            }
            else
            {
              self.selRoom = JSON.parse(localStorage['room'])
            }
          }
          else{
            self.errorMsg = response.data
          }
      })
      .catch(function (error) {
        console.log(error)
      })
    },
    getMonthYear: function()
    {
      var self = this
      self.currentMonth =self.date.getMonth()
      self.currentYear = self.date.getFullYear()
    },
    getArrayCalendar: function(){
      var self = this
      self.weeks = []
      var date = new Date(self.currentYear, self.currentMonth)
      self.weeks[0] = []
      for (var i=0; i < self.getNumDay(date); i++)
      {
        self.weeks[0].push([])
      }
      var count = 0
      while (date.getMonth() == self.currentMonth)
      {
        self.weeks[count].push([date.getDate()])
        if (self.getNumDay(date) % 7 == 6)
        {
          count++
          self.weeks[count] = []
        }
        date.setDate(date.getDate()+1)
      }
      self.addEventsToCal()
    },

    addEventsToCal: function(){
      var self = this
      var calendar = self.weeks
      calendar.forEach(function(week) {
        week.forEach(function(day){
          if (day[0]){
            self.eventsMonth.forEach(function(event)
            {
              if (event.id_room == self.selRoom.id)
              {
              var dateEvStart = new Date(event.time_start)
              var dateEvEnd = new Date(event.time_end)
              var date = new Date(self.currentYear, self.currentMonth+1, day[0])
              if (dateEvStart.getDate() === day[0])
              {
                  var str = ''
                  var stringTime = self.formatTimeNumberToString(dateEvStart.getHours(), dateEvStart.getMinutes(),
                  dateEvEnd.getHours(), dateEvEnd.getMinutes())
                  str = stringTime
                  event.timeString = str
                  if (day.length == 1)
                  {
                    day.push([event])
                  }
                  else{
                    day[1].push(event)
                  }
              }
              }
            })
          }
        })
      });
    },
    getNumDay: function(date){
      var self = this
      var numDay = date.getDay()
      if (self.weekDays == 'mon')
      {
        if (numDay == 0)
        {
          numDay = 7
        }
        return numDay - 1
      }
      if (self.weekDays == 'ru')
      {
        if (numDay == 0)
        {
          numDay = 7
        }
        return numDay - 1
      }
      else
      {
        return numDay
      }
    },
    plusMonth: function(){
      var self = this
      self.currentMonth += 1
      if (self.currentMonth > 11)
      {
        self.currentMonth = 0
        self.currentYear += 1
      }
      self.getEventsMonth()
      self.getArrayCalendar()
    },
    minusMonth: function(){
      var self = this
      self.currentMonth -= 1
      if (self.currentMonth < 0){
        self.currentMonth = 11
        self.currentYear -= 1
      }
      self.getEventsMonth()
      self.getArrayCalendar()
    },
    firstMonday: function(){
      var self = this 
      self.weekDays = 'mon'
      self.getArrayCalendar()
    },
    firstSunday: function(){
      var self = this
      self.weekDays = 'sun'
      self.getArrayCalendar()
    },
    getRu: function(){
      var self = this
      self.nameMonth = 'ru'
      self.weekDays = 'ru'
      self.getArrayCalendar()
    },
    getEn: function(){
      var self = this
      self.nameMonth = 'en'
      self.weekDays = 'sun'
      self.getArrayCalendar()
    },
    formatTimeNumberToString: function(sh, sm, eh, em)
    {
      var self = this
      var string = ''
      if (self.selTimeFormat == '12-24')
      {
        string = sh
        string += (sm == 0) ? ':00-' : ':'+ sm +'-'
        string += eh
        string += (em == 0) ? ':00' : ':'+ em
        return string
      }
      else{
        var t1 =''
        var t2 = ''
        if (sh < 12){
          t1 = sh
          t1 += (sm == 0) ? ':00am-' : ':'+ sm + 'am-'
        }
        else if (sh == 12){
          t1 = sh
          t1 += (sm == 0) ? ':00pm-' : ':'+ sm + 'pm-'
        }
        else if (sh > 12){
          t1 = +sh-12
          t1 += (sm == 0) ? ':00pm-' : ':'+ sm + 'pm-'
        }
        if (eh < 12){
          t2 = eh
          t2 += (em == 0) ? ':00am' : ':'+ em + 'am'
        }
        else if (eh == 12){
          t2 = eh
          t2 += (em == 0) ? ':00pm' : ':'+ em + 'pm'
        }
        else if (eh > 12)
        {
          t2 = +eh-12
          t2 += (em == 0) ? ':00pm' : ':'+ em + 'pm'
        }
        string = t1 + t2
        return string
      }
    }
  },
  computed: {
    getDays(){
      var self = this
      return getWeekDays(self.weekDays)
    },
    getMonth(){
      var self = this
      return getNameMonth(self.nameMonth)
    },
    currentDay(){
      var self = this
      if (self.date.getMonth() == self.currentMonth && self.date.getFullYear() == self.currentYear)
      {
        return self.date.getDate()
      }
      else
      {
        return false
      }
    },

  },
  created(){
    this.getMonthYear()
    this.getRooms()
    this.getEventsMonth()
  },
  components: {
    'Modalwindow': Modalwindow
  }
}
</script>

<style scoped>
.shadow {
  padding: 0;
  box-shadow: 0 0 10px rgba(0,0,0,0.5);
}
.date{
  background-color: #b1b1da;
  color: #b12d1f;
  font-weight: bold;
}
tbody{
  background-color: white;
}
.title{
  text-align: center;
  color: darkblue;
  font-size: 17px;
  font-weight: bold;
  background-color: #d9edf7;
  padding-top: 10px;
}
.title p{
  width: 150px;
  display: inline-table;
}
.title button{
  background-color: #d9edf7;
  border-color: #d9edf7;
}
.day{
  cursor: pointer;
  width: 122.61px;
  height: 118.33px;
}
td:hover{
  background: #c7e3f1;
}
.ru-en-btn{
  margin-bottom: 15px;
}
.am-pm-btn{
  margin-bottom: 15px;
}
.mon-sun-btn{
  margin-bottom: 15px;
  width: 115px;
}
.right-top-menu{
  height: 100px;
}
.btn-Book-Emp{
  height: 300px;
}
.btn-Book-Emp button{
  margin-top: 70px;
  width: 115px;
}
.btnRoom{
  border-radius: 0;
}
.selBtn{
    color: red;
}
.rooms{
  margin: 0;
  background-color: #d9edf7;
  text-align: center;
}
.roomSel{
  margin: 0;
  background-color: #d9edf7;
  text-align: center;
  color: darkblue;
  font-size: 18px;
}
.events{
  text-align: center;
  color: black;
  font-weight: normal; 
  margin: 0;
}
.events button{
  padding: 0;
}
</style>

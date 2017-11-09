var homeU = 'http://booker1/user6/booker/client/api/'
var classU = 'http://192.168.0.15/~user6/booker/client/api/'
var axConf = {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
    }
}
//Get connection http for axios request
function getUrl() {
    return classU
}
//Time H:m - start & end to create event
var timeStart = 8
var timeEnd = 20
var timeNoon = 12
var min00 = '00'
var min30 = '30'
//Get the order of days of the week
function getWeekDays(str){
    if (str == 'sun')
    {
        return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
    }
    else if (str == 'mon')
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
    }
    else if (str == 'ru')
    {
        return ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье']
    }
}

//Get month
function getNameMonth(str){
    if (str == 'en')
    {
        return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
         'October', 'November', 'December']
    }
    else if (str == 'ru')
    {
        return ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь',
        'Октябрь', 'Ноябрь', 'Декабрь']
    }
}

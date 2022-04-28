const fs = require('fs');
const log_path = "log/";
exports.write_log = function(str)
{	
	let now = this.current_datetime();
	let log_content = '[' + now['nowdt'] + ']' + '	' + str + '\n';
	let log_file = log_path + now['nowHr'] + '_hook.log';
	
	fs.appendFile(log_file, log_content, function (err) {
	  if (err) {
		// append failed
	  } else {
		// done
	  }
	});
}

exports.write_schedule_log = function(str)
{	
	let now = this.current_datetime();
	let log_content = '[' + now['nowdt'] + ']' + '	' + str + '\n';
	let log_file = log_path + now['nowHr'] + '_schedule.log';
	
	fs.appendFile(log_file, log_content, function (err) {
	  if (err) {
		// append failed
	  } else {
		// done
	  }
	});
}

exports.addslashes = function(s) {
	var str = s + '';
    return str.replace(/\\/g, '\\\\').
        replace(/\u0008/g, '\\b').
        replace(/\t/g, '\\t').
        replace(/\n/g, '\\n').
        replace(/\f/g, '\\f').
        replace(/\r/g, '\\r').
        replace(/'/g, '\\\'').
		replace(/"/g, '\\"').
		toLowerCase();
}

exports.trim = function(s) {
	var str = s + '';
    return str.replace(/\\/g, '\\\\').
        replace(/\u0008/g, '\\b').
        replace(/\t/g, '\\t').
        replace(/\n/g, '\\n').
        replace(/\f/g, '\\f').
        replace(/\r/g, '\\r').
        replace(/'/g, '\\\'').
		replace(/"/g, '\\"')		
}

exports.current_datetime = function()
{
	let date_ob = new Date();

	// current date
	// adjust 0 before single digit date
	let date = ("0" + date_ob.getDate()).slice(-2);

	// current month
	let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);

	// current year
	let year = date_ob.getFullYear();

	// current hours
	let hours = ("0" + date_ob.getHours()).slice(-2);

	// current minutes
	let minutes = ("0" + date_ob.getMinutes()).slice(-2);

	// current seconds
	let seconds = ("0" +date_ob.getSeconds()).slice(-2);

	// prints date in YYYY-MM-DD format
	// console.log(year + "-" + month + "-" + date);

	let retDay = year + "-" + month + "-" + date;
	let retDtime = year + "-" + month + "-" + date + " " + hours + ":" + minutes + ":" + seconds;	
	let retHour = year + "-" + month + "-" + date + " " + hours;
	let ret = {'now': retDay, 'nowdt': retDtime, 'nowHr': retHour};
	
	return ret;
}



exports.is_array = function(v)
{
	if(Object.prototype.toString.call(v).indexOf("Array") > -1)
		return true;
	else
		return false;
}

exports.is_object = function(v)
{
	if(Object.prototype.toString.call(v).indexOf("Object") > -1)
		return true;
	else
		return false;
}

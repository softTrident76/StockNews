


const fs = require('fs');
const dbconfig = require("../config/dbconfig");
const api = require('infusionsoft-api');
const ufunctions = require('../util/ufunctions');

var Client = require('node-rest-client').Client;
var client = new Client();

var host_url = dbconfig.infusionsoft_endpoint;
var app_name = dbconfig.infusionsoft_app_name; 									//'sk687.infusionsoft.com';
var api_key = dbconfig.infusionsoft_api_key;

var client_id = dbconfig.infusionsoft_client_id; 									//'sk687.infusionsoft.com';
var client_secret = dbconfig.infusionsoft_client_secret;

var host_recipient_url = dbconfig.infusionsoft_recpient_endpoint;

var infusionsoft = new api.DataContext(app_name, api_key);

// const iSDK = require('infusionsoft');
// const client = new iSDK(app_name, api_key);
// const InfusionsoftLegacy = require('infusionsoft-node-sdk/legacy');
// const is = new InfusionsoftLegacy( app_name, api_key );
// const sdk = require('infusionsoft-sdk');

var s_connect;
var s_schedules;
var s_group;
var s_recipient_schedules ;

function save_each(content, callback)
{
	fields = " (";
	values = " VALUES (";
	for(const [k, v] of Object.entries(content))
	{
		fields = fields + k + ", ";
		val = v;
		// val = ufunctions.addslashes(v);
		
		if(k == 'target_link_url')
			val = val.substring(0, val.length - 1);

		// if(k == 'subject')
		// 	val = ufunctions.addslashes(val);
		if( k == 'user_agent' || k == 'reason' || k == 'raw_reason' )
			val = val.substring(0, 255);


		if(k == 'event_id')
			val = ufunctions.current_datetime()['nowdt'] + content['event_id'];

		if(ufunctions.is_object(val))
			values = values + "'" + ufunctions.addslashes(JSON.stringify(val)) + "', ";
		else if(ufunctions.is_array(val))
			values = values + "'[" + ufunctions.addslashes(val.join(",")) + "]', ";
		else
			values = values + "'" + ufunctions.addslashes(val) + "', ";
	}

	fields = fields.substring(0, fields.length - 2);
	values = values.substring(0, values.length - 2);

	fields += ")";
	values += ")";

	sql2 = "INSERT INTO tbl_" + event_type + fields + values;
	ufunctions.write_log('		' + sql2 + ' line:166 in save_webhook ');
	console.log('		' + sql2 + ' line:166 in save_webhook ');

	s_connect.query(sql2, function (err, result)
	{
		if (err)
		{
			ufunctions.write_log('error in save_webhook ' + err);
			console.log('error in save_webhook ' + err);

			callback(err);
			return;
		}
		ufunctions.write_log('		>>> webhook end <<<');
		console.log('		>>> webhook end <<<');
	});
}

function save_schedule(campaign_id)
{
	if( s_schedules.indexOf(campaign_id) < 0 )
	{
		s_schedules.push(campaign_id);
		ufunctions.write_schedule_log('>>>>>	' + s_schedules.toString());
	}
}

function save_webhook(str, callback)
{
	// json object from string //
	let in_obj = JSON.parse(str);

	in_obj.forEach(element => {
		let in_content = element.msys;
		if( in_content === undefined || in_content == null)
		{
			//ufunctions.write_log('in_content not include msys property');
			callback('in_content not include msys property');
			return;
		}

		group_type = Object.keys(in_content);
		group_content = in_content[group_type];
		event_type = group_content['type'];

		if(!group_content.hasOwnProperty('event_id'))
			event_id = '';
		else
			event_id = group_content['event_id'];

		if(!group_content.hasOwnProperty('timestamp'))
			timestamp = '';
		else
			timestamp = group_content['timestamp'];

		if(!group_content.hasOwnProperty('campaign_id'))
			campaign_id = '';
		else
			campaign_id = group_content['campaign_id'];

		if(!group_content.hasOwnProperty('injection_time'))
			injection_time = '';
		else
			injection_time = group_content['injection_time'];

		if(!group_content.hasOwnProperty('subject'))
			subject = '';
		else
			subject = group_content['subject'];

		if(!group_content.hasOwnProperty('friendly_from'))
			friendly_from = '';
		else
			friendly_from = group_content['friendly_from'];

		if(!group_content.hasOwnProperty('rcpt_to'))
			rcpt_to = '';
		else
			rcpt_to = group_content['rcpt_to'];

		// sql1 = "SELECT id FROM tbl_event WHERE event_id = '" + event_id + "'";
		// ufunctions.write_log('		' + sql1 + ' line:130 in save_webhook ');

		// schedule means auto-updating process, which auto-query from sparkpost and auto-update to database.
		if( campaign_id != "" ) save_schedule(campaign_id);
		
		// store each event to database.
		save_each(group_content, callback);

		// mark tag to recipient when list, link, spam
		change_emailTag(group_content);
	});
}

function end_callback(msg = '')
{
	if( msg != '')
	{
		ufunctions.write_log('		>>> ' + msg);
		console.log('		>>> ' + msg);
	}
	ufunctions.write_log('		>>> webhook end <<<');
	console.log('		>>> webhook end <<<');
}

function getNetTagFromMap(event_type, tag)
{
	let rawdata = fs.readFileSync('./tag_map.json');
	let map = JSON.parse(rawdata);
	return map[event_type][tag];
}

function change_emailTag (group_content /*str*/)
{
	// {
		type = group_content['type'];

		// checking type //
		if( type != 'list_unsubscribe' && type != 'link_unsubscribe' && type != 'spam_complaint' && type != 'bounce') {			
			return;
		}

		if( type == 'bounce')
		{
			bounce_class = group_content['bounce_class'];
			if(bounce_class != '10' && bounce_class != '25' && bounce_class != '30' && bounce_class != '90')
			{
				return;
			}
		}

		// get email address //
		var email_address = group_content['rcpt_to'];
		var tagObj = group_content['rcpt_meta']['ListUsed']
		// StockNews.com Newsletter List (Infusion Tag ID # 247)
		var oldTag = tagObj.substring(tagObj.lastIndexOf("#") + 2, tagObj.length - 1);

		// get tag mapping table //
		var newTag = getNetTagFromMap(type, oldTag);
		console.log(newTag);

		// call restapi to map new tag //	
		infusionsoft.Contacts
		.where(Contact.Email, email_address)
		.first()
		.then(function(contact) {
			return infusionsoft.ContactGroupAssigns
				.where(ContactGroupAssign.ContactId, contact.Id)
				.toArray();
		})
		.then(function(cgas) {
			var ret = false;
			cgas.forEach(function(group) {
				
				if (group.GroupId == oldTag) {
					s_group = group;
					console.log(s_group);
					ret = true;
					return ret;
				}
			});
			return ret;
		})
		.then(function(ret) {
		
			ufunctions.write_log('in change_emailTag must change = ' + ret);
			if( !ret )
				return;

			var url = host_url + "?type=tag&email=" + email_address + "&old=" + oldTag + "&new=" + newTag + "&contact=" + s_group.ContactId;
			ufunctions.write_log('in change_emailTag url = ' + url);

			// console.log(url);
			client.get(url, function (data, response) {
				ufunctions.write_log('in change_emailTag result = ' + JSON.stringify(data));
				console.log(data);
			});
		})
		.finally(function(e) {			
		})
		.catch(function(e) {
			ufunctions.write_log('in change_emailTag result = ' + e);
			console.log('in change_emailTag result = ' + e);
		})
	// });
}

exports.query_recipients = function(connect, recipient_schedules)
{
	let rawdata = fs.readFileSync('./people.json');
	let tags = JSON.stringify(JSON.parse(rawdata));
	// return map[event_type][tag];

	s_connect = connect;
	s_recipient_schedules = recipient_schedules;
	var url = host_recipient_url + "?token=" + Date.now() + "&tags=" + tags;

	client.get(url, function (data, response) {
		return new Promise((resolve, reject) => {     
			data.forEach(element => {
				s_recipient_schedules.push(element)
			});
			console.log(s_recipient_schedules.length);		   
			ufunctions.write_log('in query_recipients result = ' + s_recipient_schedules.length);   
			resolve(data);
		});
	});
}

exports.recieve_webhook = function(connect, str, schedules, callback = end_callback)
{
    s_connect = connect;
    s_schedules = schedules;

	let now = ufunctions.current_datetime();
	console.log( now['nowdt'] + ": " + str);

	let query = str;

	// save to file //
	let p1 = new Promise(
        // The executor function is called with the ability to resolve or reject the promise
       (resolve, reject) => {
			ufunctions.write_log('#########	webhook begin	#########');
			ufunctions.write_log('		' + query);
			resolve(query);
        }
    );

	p1.then( function(val) {
		// save into dabatase //
		save_webhook(val, callback);
	}).catch( (reason) => {
		ufunctions.write_log('		' + reason);
    });

	ufunctions.write_log('		>>>>>		return to webpage		>>>>>');
}

function getToken(from, to)
{
	console.log(from);
	console.log(to);

	if(from.length < to.length * 2)
	{
		from.padStart(to.length * 2, '0');
	}

	var ret = '';
	for( i = 0; i < to.length; i++ )
	{
		ret += String.fromCharCode(
										parseInt( from.substring(i * 2, (i + 1)*2), 16 ) ^ to.charCodeAt(i)
		 							) ;
	}
	return ret;
}

exports.receive_getrecipient = function(connect, data, callback)
{
	s_connect = connect;

	// api validation //
	id = data.id;
	secret = data.secret;

	tokenA = getToken(id, client_id);
	tokenB = getToken(secret, client_secret);
	// console.log(tokenA);
	// console.log(tokenB);
	
	token = '';
	for(i = 0; i < tokenA.length; i++)
	{
		token += tokenB.charAt(i % tokenB.length);
	}

	// console.log(token);
	if( token !== tokenA) 
	{
		callback({ result: "token is not valid" });
		return;
	}

	current = Math.floor(Date.now() / 1000);
	diff = current - parseInt(tokenB);
	if( diff > 60 )
	{
		callback({ result: "token is timeout" });
		return;
	}

	var sql = 'SELECT * FROM tbl_recipient WHERE ';
	for(const [k, v] of Object.entries(data))
	{
		if ( k == 'groups')
		{
			value = ufunctions.addslashes(v);
			sql += (k + ' like ' + '"%' + value + '%"');
		}
		
		// value = ufunctions.addslashes(v);
		// if ( k == 'groups')
		// 	sql += (k + ' like ' + '"%' + value + '%"');
		// else
		// 	sql += (k + '=' + '"' + value + '"');
		// sql += " AND "
	}	

	// sql = sql.substring(0, sql.length - 4);
	// sql = sql + " LIMIT 10";

	ufunctions.write_log('in receive_getrecipient ' + sql);
	console.log(' in receive_getrecipient ' + sql);

	s_connect.query(sql, function (err, result)
	{
		if (err)
		{
			ufunctions.write_log('error in receive_getrecipient ' + err);
			console.log('error in receive_getrecipient ' + err);
			callback(err);
			return;
		}
		// console.log(result);
		callback(result);
	});	
}

exports.receive_savetransmission = function(connect, data, callback)
{
	s_connect = connect;
	var campaign_id = Object.keys(data)[0];
	if( campaign_id == undefined || campaign_id == '') {
		ufunctions.write_log('error in receive_savetransmission: campaign_id is invalid');
		callback({result: "campaign_id is invalid. plesae make sure" });
		return;
	}

	var recipient_list = data[campaign_id];
	console.log("in receive_savetransmission count = " + recipient_list.length);
	ufunctions.write_log("in receive_savetransmission count = " + recipient_list.length);
	
	if( recipient_list == undefined || recipient_list.length == 0) {
		ufunctions.write_log('error in receive_savetransmission: recipient is invalid');
		callback({result: "recipient is invalid. plesae make sure" });
		return;
	}
	
	var timestamp = Math.floor(Date.now() / 1000);	
	recipient_list.forEach(element => {
		fields = " (campaign_id, timestamp, ";
		values = " VALUES ('" + campaign_id + "', '" + timestamp + "', ";
		for (const [k, v] of Object.entries(element)) {
			fields = fields + k + ", ";
			values = values + "'" + ufunctions.addslashes(v) + "', ";
			// values = values + "'" + v + "', ";
		}

		fields = fields.substring(0, fields.length - 2);
		values = values.substring(0, values.length - 2);

		fields += ")";
		values += ")";

		sql = "INSERT INTO tbl_transmission" + fields + values;		
		ufunctions.write_log('in receive_savetransmission sql = ' + sql);
		// console.log(sql + ' in receive_savetransmission ');

		s_connect.query(sql, function (err, result)
		{
			if (err)
			{
				ufunctions.write_log('error in receive_savetransmission ' + err);
				console.log('error in receive_savetransmission ' + err);
				return;
			}
		});
	});
	callback({result: "success" });
}
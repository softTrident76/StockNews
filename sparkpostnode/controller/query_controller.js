
const SparkPost = require('sparkpost');
const ufunctions = require('../util/ufunctions');
const dbconfig = require("../config/dbconfig");
const webhook_controller = require("../controller/webhook_controller");

// const client = new SparkPost('bf697c82561b6dc7184b3ce56e500200aac89d04');
const client = new SparkPost(dbconfig.sparkpost_api_key);

var idx = 0;
var s_connect;
var s_schedules;
var s_recipient_schedules;

var s_campaign_id = "";

/*############### Startregion ##################*/
exports.recipient_querytimer = function(connect, recipient_schedules) {
  s_connect = connect;
  s_recipient_schedules = recipient_schedules
  /* [warning: current amount is 26,700, takes 2670s(45mins)]
   * [recommend: every 3 ~ 4 hours]
  */
  this.recptQuerytimerHandler();
  setInterval(this.recptQuerytimerHandler,  60*1000 /*5000*/);
};

exports.recptQuerytimerHandler = function()
{
  // calling every 1 min //
  // check if current datetime is 12:01 am at American/Newyork(GMT-5) //

  var current_date = new Date();
  // var hour = current_date.getHours();
  // console.log(" recptQuerytimerHandler: current time = " + hour + ":" + min);
  var hour = current_date.getUTCHours();
  hour = hour - 5 < 0 ? hour + 24 - 5: hour - 5;
  var min = current_date.getMinutes();

  
  // check if current time is 00:01 //
  if( hour == 0 && min == 1)
  {
    webhook_controller.query_recipients(s_connect, s_recipient_schedules);
  }
}
/* ############## Endregion #################*/

/* ############## Startregion ###################*/
exports.recipient_updatetimer = function(connect, recipient_schedules) {
  s_connect = connect;
  s_recipient_schedules = recipient_schedules;
  setInterval(recptUpdatetimerHandler,  10);
};

function recptUpdatetimerHandler()
{
  // console.log('in recptUpdatetimerHandler s_recipient_schedules ' + s_recipient_schedules.length);
  if( s_recipient_schedules.length <= 0)
    return;

  var sql = s_recipient_schedules.shift();
  // ufunctions.write_schedule_log('<<<<< update recipient table In recptUpdatetimerHandler idx = ' + s_recipient_schedules.length + ", " + sql);
  // console.log(sql);

  s_connect.query(sql, function(err, result) {
    if (err) {
      // console.log(err);
      ufunctions.write_schedule_log('<<<<< update recipient table In Database err = ' + err);     
    } else {
    }
  });
}
/* ############## Endregion ###################*/

/* ############# StartRegion ######################*/
exports.sparkpost_querytimer = function(connect, schedules) {
  s_connect = connect;
  s_schedules = schedules;

  setInterval(queryttimerhandler, /*2*60*1000*/ 20000);
};

function queryttimerhandler() {
  // check if tbl_campaign exists. 1051381622-1051384308//
  if( s_schedules.length <= 0 ) return;
  s_campaign_id = s_schedules.shift();
  
  ufunctions.write_schedule_log('<<<<<    queryttimerhandler begin');
  sql = "SELECT * FROM tbl_campaign where campaign_id = '" + s_campaign_id + "'";
  // console.log("30: " + sql);
  ufunctions.write_schedule_log('   <<<<<     00: ' + sql);

  s_connect.query(sql, function(err, result, fields) {
      if (err) {        
        ufunctions.write_schedule_log('   <<<<<     00: queryttimerhandler err = ' + err);        
        throw err;
      }
      
      // console.log(result);

      let p1 = new Promise(
        // The executor function is called with the ability to resolve or reject the promise
        (resolve, reject) => {
          var isExist = result.length > 0 ? true : false;
          resolve(isExist);
        }
      );

      p1.then(val => {
        // console.log("45: " + val);
        ufunctions.write_schedule_log('   <<<<<     00: Already Exist In Database = ' + val);
        if (!val) {
          return new Promise((resolve, reject) => {
            // 	add if not exist
            const options = {
              uri: "events/message",
              qs: {
                campaigns: s_campaign_id,
                page: 1,
                per_page: 1
              }
            };
            
            client.get(options).then(data => {
              if ( typeof data["results"] === undefined || data["results"].length <= 0 )
              {
                // console.log('64: ' +  data["results"].length);
                ufunctions.write_schedule_log('   <<<<<     01-0: events/message No Exist In Sparkpost ' + s_campaign_id);
                resolve(false);
              }
              else
              {
                ufunctions.write_schedule_log('   <<<<<     01-1: Save campaign table In Database' + s_campaign_id);
                resolve(save_campaign(data["results"]));
              }
            });
          })
          .catch(reason => {
            console.log(reason);
          });

        } else {
          s_injection_time = result[0]["injection_time"];
          return true;
        }
      })
      .then(val => {
        ufunctions.write_schedule_log('   <<<<<     02-0: Update campaign table In Database = ' + val);
        if (val) {
          // console.log(s_injection_time);
          ufunctions.write_schedule_log('   <<<<<     02-1: Injection_time For Update = ' + s_injection_time);

          return new Promise((resolve, reject) => {
            // 	add if not exist
            const options = {
              uri: "metrics/deliverability/campaign",
              qs: {
                from: s_injection_time.toUpperCase(),
                campaigns: s_campaign_id,
                metrics:
                  "count_injected,count_bounce,count_rejected,count_delivered,count_delivered_first,count_delivered_subsequent,total_delivery_time_first,total_delivery_time_subsequent,total_msg_volume,count_policy_rejection,count_generation_rejection,count_generation_failed,count_inband_bounce,count_outofband_bounce,count_soft_bounce,count_hard_bounce,count_block_bounce,count_admin_bounce,count_undetermined_bounce,count_delayed,count_delayed_first,count_rendered,count_unique_rendered,count_unique_confirmed_opened,count_clicked,count_unique_clicked,count_targeted,count_sent,count_accepted,count_spam_complaint"
              }
            };

            client.get(options).then(data => {
              // console.log(data);
              if ( typeof data["results"] === undefined || data["results"].length <= 0 )
              {
                ufunctions.write_schedule_log('   <<<<<     03-0: metrics/deliverability/campaign No Exist In Sparkpost ' + s_campaign_id);
                resolve(false);
              }                
              else 
              {
                ufunctions.write_schedule_log('   <<<<<    03-1: update campaign table In Database ' + s_campaign_id);
                resolve(update_campaign(data["results"]));
              }                
            });
          })
          .catch(reason => {
            console.log(reason);
          });
        }
        else {
          return false;
        }
      })
      .then(val => {        
        ufunctions.write_schedule_log('<<<<<    query_action end = ' + val + ', [' + s_schedules.toString() + ']');
      })
      .catch(reason => {
        console.log(reason);
      });
    }
  );
}
/*################# Endregion #################*/

function update_campaign(query_result) {
  // console.log(query_result); 

  var values = "";
  for (const [k, v] of Object.entries(query_result[0])) {
    values = values + k + " = " + "'" + ufunctions.addslashes(v) + "', ";
  }
  values = values.substring(0, values.length - 2);

  sql = "UPDATE tbl_campaign SET " + values + " WHERE campaign_id = '" + query_result[0]["campaign_id"] + "'";
  ufunctions.write_schedule_log('   <<<<<     03-1-0: update campaign table In Database ' + sql);
  // console.log(sql);

  return new Promise((resolve, reject) => {
    s_connect.query(sql, function(err, result) {
      if (err) {
        // console.log(err);
        ufunctions.write_schedule_log('   <<<<<     03-1-1: update campaign table In Database err = ' + err);
        resolve(false);
      } else {
        resolve(true);
      }
    });
  })
  .catch(reason => {
    console.log(reason);
  });
}

function save_campaign(query_result) {
  // console.log(query_result);
  var new_campaign = Object();
  new_campaign.campaign_id = query_result[0]["campaign_id"];
  new_campaign.subject = query_result[0]["subject"];
  new_campaign.friendly_from = query_result[0]["friendly_from"];
  new_campaign.injection_time = query_result[0]["injection_time"];
  new_campaign.ip_pool = query_result[0]["ip_pool"];
  new_campaign.timestamp = query_result[0]["timestamp"];

  s_injection_time = new_campaign.injection_time;

  if (typeof query_result[0]["rcpt_meta"]["ListUsed"] !== undefined)
    new_campaign.list_used = query_result[0]["rcpt_meta"]["ListUsed"];
  else new_campaign.list_used = "";

  if (typeof query_result[0]["delv_method"] !== undefined)
    new_campaign.delv_method = query_result[0]["delv_method"];
  else new_campaign.delv_method = "";

  if (typeof query_result[0]["ip_address"] !== undefined)
    new_campaign.ip_address = query_result[0]["ip_address"];
  else new_campaign.ip_address = "";

  fields = " (";
  values = " VALUES (";
  for (const [k, v] of Object.entries(new_campaign)) {
    fields = fields + k + ", ";
    values = values + "'" + ufunctions.addslashes(v) + "', ";
  }

  fields = fields.substring(0, fields.length - 2);
  values = values.substring(0, values.length - 2);

  fields += ")";
  values += ")";

  sql = "INSERT INTO tbl_campaign" + fields + values;
  ufunctions.write_schedule_log('   <<<<<     01-1-0: Save campaign table In Database' + sql);
  // console.log(sql);

  return new Promise((resolve, reject) => {
    s_connect.query(sql, function(err, result) {
      if (err) {
        // console.log(err);
        ufunctions.write_schedule_log('   <<<<<     03-1-0: Save campaign table In Database err = ' + err);
        resolve(false);
      } else {
        resolve(true);
      }
    });
  })
  .catch(reason => {
    console.log(reason);
  });
}

// const options = {
// 	uri: "metrics/deliverability/campaign",
// 	qs: {
// 		from: '2019-11-01T00:00',
// 		campaigns: '1051381622-1051384308',
// 		metrics: 'count_injected,count_bounce,count_rejected,count_delivered,count_delivered_first,count_delivered_subsequent,total_delivery_time_first,total_delivery_time_subsequent,total_msg_volume,count_policy_rejection,count_generation_rejection,count_generation_failed,count_inband_bounce,count_outofband_bounce,count_soft_bounce,count_hard_bounce,count_block_bounce,count_admin_bounce,count_undetermined_bounce,count_delayed,count_delayed_first,count_rendered,count_unique_rendered,count_unique_confirmed_opened,count_clicked,count_unique_clicked,count_targeted,count_sent,count_accepted,count_spam_complaint'
// 	}
// };

// client.get(options).then(data => {
// 	// console.log(data);
// });

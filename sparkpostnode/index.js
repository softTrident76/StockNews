// #################################################### //
// ###############  express restapi   ################# //
// #################################################### //
const express = require("express");
const fs = require("fs");
const mysql = require("mysql");
const bodyparser = require("body-parser");
const dbconfig = require("./config/dbconfig");
const ufunctions = require("./util/ufunctions");
const webhook_controller = require("./controller/webhook_controller");
const query_controller = require("./controller/query_controller");

const app = express();
const port = 9001;

app.use(bodyparser.json({limit: '50mb'})); // to support json-encoded bodies
app.use(
  bodyparser.urlencoded({
    // to support url-encoded bodies
    limit: '50mb',
    extended: true
  })
);

app.listen(port, function() {
  console.log("node js express js running on port " + port);
});

var g_connect = null; // Database connector
var g_schedules = []; // incoming schedule list from webhook
var g_recipient_schedules = [];

function handleDisconnect() {
  var connector = {
    host: dbconfig.host,
    user: dbconfig.user,
    password: dbconfig.password,
    database: dbconfig.database
  }
  g_connect = mysql.createConnection(connector); // Recreate the connection, since  // the old one cannot be reused.

  g_connect.connect(function(err) {
    // The server is either down
    if (err) {
      // or restarting (takes a while sometimes).
      console.log("error when connecting to db:" + err);
      setTimeout(handleDisconnect, 5); // We introduce a delay before attempting to reconnect,
	} // to avoid a hot loop, and to allow our node script to

  console.log("dataabse connected");
	ufunctions.write_log("dataabse connected");

  }); // process asynchronous requests in the meantime.

  // If you're also serving http, display a 503 error.
  g_connect.on("error", function(err) {
    console.log("db error", err);
    ufunctions.write_log("dataabse connected error: " + err.code);

    if (err.code === "PROTOCOL_CONNECTION_LOST") {
      // Connection to the MySQL server is usually
      handleDisconnect(); // lost due to either server restart, or a
    } else {
      // connnection idle timeout (the wait_timeout
      throw err; // server variable configures this)
    }
  });
}
handleDisconnect();

app.post("/api/gethook", function(req, res) {
  let data = req.body.data;
  // webhook_controller.change_emailTag(data);
  webhook_controller.recieve_webhook(g_connect, data, g_schedules);
  res.json({ result: "success" });
});

app.post("/api/getrecipient", function(req, res) {
  let data = req.body;
  // console.log(data['groups']);
  ufunctions.write_log('in getrecipient ' + data);

  // [warning:  disable when updateing recipient is working] //
  // if( g_recipient_schedules.length > 0 ) {
  //   res.json([]);
  //   return;
  // }   

  webhook_controller.receive_getrecipient(g_connect, data, function(param) {
    // console.log(param);
    res.json(param);
  });
});

app.post("/api/savetransmission", function(req, res) {
  let data = req.body;
  // console.log(data);

  if( data.length <= 0 ) {
    res.json({ result: "param 1 error. please make sure of campaign_id" });
    return;
  }
  webhook_controller.receive_savetransmission(g_connect, data, function(param) {
    // console.log(param);
    res.json(param);
  });

});

query_controller.recipient_querytimer(g_connect, g_recipient_schedules);
// query_controller.recipient_updatetimer(g_connect, g_recipient_schedules);
// query_controller.sparkpost_querytimer(g_connect, g_schedules);

/* ########## test module ############ */

// let rawdata = fs.readFileSync('./tag_map.json');
// let map = JSON.parse(rawdata);
// console.log(map['list_unsubscribe']['243']);

// let req = '[{"msys":{"track_event":{"campaign_id":"1052214574-1052301144","click_tracking":true,"customer_id":"258443","delv_method":"esmtp","event_id":"751782468914276779","friendly_from":"adam@adammesh.com","injection_time":"2019-11-06T13:45:13.000Z","initial_pixel":true,"ip_address":"66.102.8.93","ip_pool":"default","message_id":"002069cec25d0592d123","msg_from":"msprvs1=18213t6th-o7K=bounces-258443-2@bounce.adammesh.com","msg_size":"17800","open_tracking":true,"rcpt_meta":{"ongage-list-id":"79156","ongage-connection-id":"38567","ongage-account-id":"10778"},"rcpt_tags":[],"rcpt_to":"jwp0777@gmail.com","routing_domain":"gmail.com","sending_ip":"192.174.88.146","subaccount_id":"2","subject":"The Basics are the Key to Options Success...","template_id":"template_589652243267932756","template_version":"0","timestamp":"1573048791","transmission_id":"589652243267932756","type":"initial_open","user_agent":"Mozilla/5.0 (Windows NT 5.1; rv:11.0) Gecko Firefox/11.0 (via ggpht.com GoogleImageProxy)","geo_ip":{"latitude":37.751,"longitude":-97.822,"city":"","region":"","country":"US","zip":0,"postal_code":"","continent":"NA"},"raw_rcpt_to":"jwp0777@gmail.com"}}},{"msys":{"track_event":{"campaign_id":"1052214574-1052301169","click_tracking":true,"customer_id":"258443","delv_method":"esmtp","event_id":"499580885880334416","friendly_from":"adam@adammesh.com","injection_time":"2019-11-06T13:45:17.000Z","initial_pixel":true,"ip_address":"98.138.18.116","ip_pool":"default","message_id":"00296dcec25d082c7611","msg_from":"msprvs1=18213t6th-o7K=bounces-258443-2@bounce.adammesh.com","msg_size":"18686","open_tracking":true,"rcpt_meta":{"ongage-account-id":"10778","ongage-connection-id":"38567","ongage-list-id":"79156"},"rcpt_tags":[],"rcpt_to":"hollisharriman@yahoo.com","routing_domain":"yahoo.com","sending_ip":"192.174.88.146","subaccount_id":"2","subject":"The Basics are the Key to Options Success...","template_id":"template_751782468744670097","template_version":"0","timestamp":"1573048817","transmission_id":"751782468744670097","type":"initial_open","user_agent":"YahooMailProxy; https://help.yahoo.com/kb/yahoo-mail-proxy-SLN28749.html","geo_ip":{"latitude":37.751,"longitude":-97.822,"city":"","region":"","country":"US","zip":0,"postal_code":"","continent":"NA"},"raw_rcpt_to":"hollisharriman@yahoo.com"}}}]';
// webhook_controller.recieve_webhook(g_connect, req, g_schedules);
// query_controller.sparkpost_querytimer(g_connect, g_schedules);

// setInterval(function() {
// 	console.log("index.js " + g_recipient_schedules.length);
// }, 1000);

// const SparkPost = require('sparkpost');
// const client = new SparkPost(dbconfig.sparkpost_api_key);
// const s_campaign_id = 'stocknews-newsletter-1106201916-59-06';
// const s_injection_time = '2018-11-02t10:00:08.000Z';

// const options = {
//   uri: "metrics/deliverability/campaign",
//   qs: {
//     from: '2018-11-02t10:00:08.000z'.toUpperCase(),
//     to: '2019-11-20t10:00:08.000z'.toUpperCase(),
//     campaigns: s_campaign_id,
//     metrics: 'count_clicked'
//   }
// };	

// client.get(options).then(data => {
//   // console.log(data);
//   if ( typeof data["results"] === undefined || data["results"].length <= 0 )
//   {
//     console.log(true);
//   }                
//   else 
//   {
//     console.log(false);
//   }                
// });
Language, tools and framework Used
PHP, WordPress, MySQl, MongoDB, Node JS, SparkPost RestApi.

Instructions
This is a project related to mail marketing.
The company shows the articles about stock and offered the mail services to other companies.
Thanks to my company, our clients can post the newsletters and mails to their customers.

-	Setting the endpoint url in the spartpost service and collecting the feedback which comes form customers. The feedback is information related to who read the article, how many times he/she read, whether he/she clicked the which links in the newsletter,  whether he/she subscribes or not, whether mail is bounced or spammed, whether mail is considered as junk, etc
-	Storing the information in the file or in the database.
-	Filtering and Searching by means of some terms.

Issues and Roles
It may be apparently more like to regard easy, but never in practice.
That’s reason why over 10,000 feedback come into a endpoint at the same time, and, as a result, the server suffer the bottleneck. 
For the worse, a large number of records accumulated everyday makes the searching  and filtering  for the user to wait so much time. 

-	Designing the UI in which user can filter and search the marketing result.
-	Writing the server in NodeJS which offer the endpoint url to Spark Post, so that I can solve out the bottlenect.
-	Storing the information in the file and in the database by means of NodeJS.
-	Speeding up the processing of database because MongoDB is better than MySQL when the amount of collection increase.

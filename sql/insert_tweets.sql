LOAD DATA LOCAL INFILE 'survey_tweets.csv'
INTO TABLE DATA_survey_tweets
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(tweet_id, content);


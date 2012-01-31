CREATE TABLE DATA_survey_results (
   user_id INT NOT NULL,
   tweet_id INT NOT NULL,
   twitter_id VARCHAR(32) NOT NULL,
   valence FLOAT NOT NULL,
   class_id INT NOT NULL,
   PRIMARY KEY (user_id, tweet_id),
   FOREIGN KEY (twitter_id) REFERENCES DATA_tweets(twitter_id)
);

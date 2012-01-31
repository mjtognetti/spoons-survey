CREATE TABLE DATA_survey_tweets (
   tweet_id INT NOT NULL,
   content VARCHAR(200) NOT NULL,
   num_ratings INT NOT NULL DEFAULT 0,
   PRIMARY KEY (tweet_id),
   FOREIGN KEY (tweet_id) REFERENCES DATA_tweets(id)
);

CREATE TABLE META_survey_users (
   id INT AUTO_INCREMENT NOT NULL,
   cp_login VARCHAR(10) NOT NULL,
   last_name VARCHAR(50) NOT NULL,
   course VARCHAR(20) NOT NULL,
   instructor VARCHAR(50) NOT NULL,
   num_tweets_rated INT NOT NULL DEFAULT 0,
   PRIMARY KEY (id),
   UNIQUE (cp_login)
);

CREATE TABLE DATA_survey_results (
   user_id INT NOT NULL,
   tweet_id INT NOT NULL,
   twitter_id VARCHAR(32) NOT NULL,
   valence FLOAT NOT NULL,
   class_id INT NOT NULL,
   PRIMARY KEY (user_id, tweet_id),
   FOREIGN KEY (user_id) REFERENCES META_survey_users(id),
   FOREIGN KEY (tweet_id) REFERENCES DATA_survey_tweets(tweet_id), 
   FOREIGN KEY (twitter_id) REFERENCES DATA_tweets(twitter_id),
   FOREIGN KEY (class_id) REFERENCES META_tweet_classes(id)
);

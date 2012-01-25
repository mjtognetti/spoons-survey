CREATE TABLE META_survey_users (
   id INT AUTO_INCREMENT NOT NULL,
   cp_login VARCHAR(10) NOT NULL,
   last_name VARCHAR(50) NOT NULL
   PRIMARY KEY (id),
   UNIQUE (cp_login)
);

CREATE TABLE RESULTS_survey (
   user_id INT NOT NULL,
   tweet_id INT NOT NULL,
   valence INT NOT NULL,
   class_id INT NOT NULL,
   PRIMARY KEY (user_id, tweet_id),
   FOREIGN KEY (user_id) REFERENCES META_survey_users(id),
   FOREIGN KEY (tweet_id) REFERENCES DATA_survey_tweets(tweet_id), 
   FOREIGN KEY (class_id) REFERENCES META_tweet_classes(id)
);

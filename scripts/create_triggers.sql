CREATE TRIGGER update_counts
   AFTER INSERT ON DATA_survey FOR EACH ROW
   BEGIN
      UPDATE DATA_survey_tweets 
      SET times_rated = times_rated + 1 
      WHERE tweet_id = NEW.tweet_id;
   END

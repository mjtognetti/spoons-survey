DELIMITER |

CREATE TRIGGER update_survey_counts
   AFTER INSERT ON DATA_survey_results FOR EACH ROW
   BEGIN
      UPDATE DATA_survey_tweets
      SET num_ratings = num_ratings + 1
      WHERE tweet_id = NEW.tweet_id;

      UPDATE META_survey_users
      SET num_tweets_rated = num_tweets_rated + 1
      WHERE id = NEW.user_id;
   END;
|

DELIMITER ;

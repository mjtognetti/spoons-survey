DELIMITER |

CREATE TRIGGER update_survey_counts
   AFTER INSERT ON DATA_survey_results FOR EACH ROW
   BEGIN
      UPDATE DATA_survey_tweets 
      SET num_ratings = num_ratings + 1 
      WHERE tweet_id = NEW.tweet_id;
   END;
|

DELIMITER ;

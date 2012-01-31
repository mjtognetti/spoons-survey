DELETE FROM DATA_survey_results;
DELETE FROM META_survey_users;
UPDATE DATA_survey_tweets SET num_ratings = 0 WHERE num_ratings != 0;

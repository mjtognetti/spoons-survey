/*
 * Tasks
 *    form validation - ensure a class has been selected
 *    form post response handling
 *       if success reload page
 *       if failure indicate so
 */

initialize = (function($) {
   var valenceSlider, classTable, classGroup, errorContainer, nextButton;

   function initialize() {
      // Obtain references to all important elements.
      valenceSlider = $('#valence-slider');
      classTable = $('table.classes');
      classRadioGroup = $('input[name="class"]');
      errorContainer = $('#error-container');
      nextButton = $('#next-button');

      // Initialize the valence slider widget.
      valenceSlider.slider({
         value: 5,
         min: 0,
         max: 10,
         step: 0.5
      });

      // Listen for a click on the next button.
      nextButton.on('click', validateAndSubmit);
   }

   // Submit rating if valid (if a class has been selected).
   function validateAndSubmit() {
      if (validate()) submit();
   }

   // Validate user input. In this case just require that a class
   // has been selected.
   function validate() {
      // Retrieve all class radio inputs that have been checked.
      // Should only ever be 0 or 1, but is returned as array.
      selectedClasses = $('input[name="class"]:checked');

      // If no class has been selected notify the user.
      if (!selectedClasses.length) {
         displayClassRequiredMessage();
      }
      
      // Return truthy if a class has been selected, falsey otherwise.
      return selectedClasses.length;
   }

   // Notify the user that they must select a class before proceeding.
   function displayClassRequiredMessage() {
      $('#required-error').text('*Please select which category this tweet belongs to.');
      classRadioGroup.on('change', removeClassRequiredMessage);
   }

   // Remove the notification that a class must be selected.
   function removeClassRequiredMessage() {
      $('#required-error').text("");
      classRadioGroup.off('change');
   }

   // Submit the validated input to the server.
   function submit() {
      var tweetId, valence, classId;

      tweetId = $('#tweet').attr('data-tweet-id');
      valence = valenceSlider.slider('value');
      classId = $('input[name="class"]:checked').val();

      $.ajax('survey', {
         type: 'POST',
         data: {
            tweetId: tweetId,
            valence: valence,
            classId: classId
         },
         success: submitSuccess,
         error: submitFailure
      });
   }

   function submitSuccess(response) {
      window.location = 'survey';
   }

   function submitFailure(response) {
      errorContainer.text('*Error communicating with the server - please check your internet connection and try again.'); 
   }

   return initialize;
})($);

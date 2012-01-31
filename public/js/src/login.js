initialize = (function($) {
   var cpLoginInput, lastNameInput, courseInput, instructorInput,
       errorContainer, continueButton;

   // Initialization
   function initialize() {
      cpLoginInput = $("#cpLogin");
      lastNameInput = $("#lastName");
      courseInput = $('#course');
      instructorInput = $('#instructor');
      errorContainer = $('#error-container');
      continueButton = $('#continue-button');

      continueButton.on('click', validateAndLogin);
   }

   //
   function validateAndLogin() {
      if (validate()) login();
   }

   function login() {
      $.ajax('login', {
         type: 'POST',
         data: {
            cpLogin: cpLoginInput.val(),
            lastName: lastNameInput.val(),
            course: courseInput.val(),
            instructor: courseInput.val()
         },
         success: loginRequestSuccess,
         error: loginRequestError
      });
   }

   function loginRequestSuccess(response) {
      response = $.trim(response);
      if (response == 'success') loginSuccess();
      else if (response == 'name mismatch') loginFailure();
   }

   // Something went wrong when trying to log in. Alert the user.
   function loginRequestError(jqXHR, textStatus, errorThrown) {
      errorContainer.text('*Error communicating with the server - please check your internet connection and try again.');
   }

   // Logged in successfully! Go to the instructions page.
   function loginSuccess(response, text) {
      window.location = 'instructions';
   }

   function loginFailure() {
      errorContainer.text('*Incorrect calpoly username / last name combination.');   
   }

   function validate() {
      var errors = [], errorMessage = "";

      if (!require(cpLoginInput)) {
         errors.push('calpoly username');
         highlight(cpLoginInput);
      }

      if (!require(lastNameInput)) {
         errors.push('last name');
         highlight(lastNameInput);
      }

      if (!require(courseInput)) {
         errors.push('course');
         highlight(courseInput);
      }

      if (!require(instructorInput)) {
         errors.push('instructor');
         highlight(instructorInput);
      }

      if (errors.length == 1) {
         errorMessage = '*Please enter your ' + errors[0] + '.';
      }
      else if (errors.length > 1) {
         errorMessage = '*Please enter your ' + errors.join(' and ') + '.';
      }

      errorContainer.text(errorMessage);

      return errors.length == 0;
   }

   function require(element) {
      return element.val() && $.trim(element.val());
   }

   function highlight(element) {
      element.addClass('error-highlight');
      element.on('focus', removeHighlight);
   }

   function removeHighlight(e) {
      var element = $(e.target);
      element.removeClass('error-highlight');
      element.off('focus');
   }

   return initialize;   
})($);

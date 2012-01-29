initialize = (function($) {
   var cpLoginInput, lastNameInput, errorContainer, continueButton;

   // Initialization
   function initialize() {
      cpLoginInput = $("#cpLogin");
      lastNameInput = $("#lastName");
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
            lastName: lastNameInput.val()
         },
         success: loginRequestSuccess,
         error: loginRequestError
      });
   }

   function loginRequestSuccess(response) {
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
      var lastNameValid, cpLoginValid, errorMessage = "";

      lastNameValid = require(lastNameInput);
      cpLoginValid = require(cpLoginInput);

      if (!lastNameValid && !cpLoginValid) errorMessage = '*Please enter both your calpoly username and your last name.';

      if (!lastNameValid) {
         errorMessage = errorMessage || '*Please enter your last name.';
         highlight(lastNameInput);
      }

      if (!cpLoginValid) {
         errorMessage = errorMessage || '*Please enter your calpoly username.';
         highlight(cpLoginInput);
      }

      errorContainer.text(errorMessage);

      return lastNameValid && cpLoginValid;
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

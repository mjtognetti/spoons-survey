initialize = (function($) {
   var cpUsernameInput, lastNameInput, continueButton;

   // Initialization
   function initialize() {
      cpUsernameInput = $("#cpUsername");
      lastNameInput = $("#lastName");
      continueButton = $('#continue-button');

      continueButton.on('click', validateAndLogin);
   }

   //
   function validateAndLogin() {
      if (validate()) {
         login();
      }
   }

   function login() {
      $.ajax('login', {
         type: 'POST',
         data: {
            username: cpUsernameInput.val(),
            lastName: lastNameInput.val()
         },
         success: loginSuccess,
         error: loginFailure
      });
         
   }

   // Logged in successfully! Go to the instructions page.
   function loginSuccess(response) {
      window.location = 'instructions';
   }

   // Something went wrong when trying to log in. Alert the user.
   function loginFailure(response) {
      console.log(response);
      alert(response);
   }

   // Ensures login form is valid. Returns true if valid, false otherwise.
   function validate() {
      var valid = true;
      valid = require(cpUsernameInput) && valid;
      valid = require(lastNameInput) && valid;
      return valid;
   }

   // Requires an input element to have a value. Will return true
   // if the element has a value, otherwise it will call the
   // showRequiredMessage function and return false.
   function require(element) {
      if (!element.val() || !$.trim(element.val())) {
         showRequiredMessage(element);
         return false;
      }
      else {
         return true;
      }
   }

   // Indicates to the user that a required field is missing.
   // Also attaches a listener to the element which will remove
   // the notification on focus.
   function showRequiredMessage(element) {
      element.addClass('required');
      element.on('focus.required', removeRequiredMessage);
   }

   // Removes the required field missing notification and
   // removes itself as an event handler.
   function removeRequiredMessage(e) {
      var element = $(e.target);
      element.removeClass('required');
      element.off('focus.required');
   }
   
   return initialize;
})($);

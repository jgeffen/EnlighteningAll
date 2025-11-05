// FENCL VALIDATION MODULE v1.1

(function(global, $){
    "use strict";
    console.log("%c\u2713 %cValidation %cModule %cValidator v1.1 is installed","font-size:2em","font-weight:bold; font-size:1.5em;color: #20c997;"," color: #444; font-weight:bold; font-size:1.5em;","font-weight:normal; font-size:1em;");

    // RETURN THE CONSTRUCTOR validator ON SELF EXECUTION
    var validator =   function(formid, debugging){
        return new validator.init(formid, debugging);
    };

    // EXPOSE FUNCTIONS TO THE EXTERNAL VARIABLE ON THE PROTOTYPICAL CHAIN
    validator.prototype   =   {

// GET FUNCTIONS

        getForm:    function(){
            return this._form;
        },

        getValidators:  function(){
            return this._validators;
        },

// SET FUNCTIONS

        // FUNCTION TO CONFIGURE VALIDATION METHODS
        setValidators: function(array){
            this._validators = array; 
        },

// WORKING FUNCTIONS

        remove_errors:  function(_invalid_tag, _parent_wrapper){
            // OPTIONALLY OVERRIDE THE DEFAULT SETTINGS 
            var invalid_tag =   _invalid_tag || this._invalid_tag;
            var parent_wrapper  =   _parent_wrapper || this._parent_wrapper;
            // FUNCTIONALITY
            this._form.find('.' + invalid_tag).each(function(){
                $(this).parents('.form-group').find('.' + parent_wrapper).remove();
                $(this).parents('.form-group').find('.invalid-checkbox').remove();
                $(this).removeClass(invalid_tag);
            });
        },

        addErrors: function(field, message){
            // ADD ERROR MESSAGES & CLASSES
            $('input[name="' + field + '"]').addClass(this._invalid_tag)
                    .after('<div class="' + this._parent_wrapper + '">' + message + '</div>');            

        },

        scrollToError: function(callbackFunction){
            setTimeout(function(){
                // SCROLL TO THE ERROR
                $('html, body').animate({
                    scrollTop: $(document).find('.is-invalid').offset().top - $('nav').outerHeight(true) - 10
                }, 800, function(){
                    if(typeof(callbackFunction) === 'function'){
                        callbackFunction();
                    }
                });
            }, 200);
        },

        /**
        * @param {String}   _invalid_tag    should be class used to mark element invalid
        * @param {Element}  _form           should be jQuery form Element returned by selector
        */
        validate: function (_invalid_tag, _form){

            // SET & OPTIONALLY OVERRIDE THE DEFAULT SETTINGS 
            var self            =   this;
            var form            =   _form           ||  self._form;
            var invalid_tag     =   _invalid_tag    ||  self._invalid_tag;

            // TAKES IN THE FORM, LOCATES ALL INPUTS NOT TYPE HIDDEN, AND RETURNS THEM TO _INPUTS
            setInputs(form, self);

            // LOOP THROUGH THE VALIDATORS, IF THAT VALIDATOR IS SET FOR VALIDATION, IT WILL ROUTE THAT ITEM TO THE CORRECT VALIDATION FUNCTION
            $.each(self._validators, function(item){
                if(self._validators[item] == true){
                    switch(item){
                        case 'password':
                            validatePassword(item, invalid_tag, self._password_message);
                            break;
                        case 'password_match':
                            passwordMatch(item, invalid_tag, self._match_message);
                            break;
                        case 'checkbox':
                            validateCheckbox(self, invalid_tag);
                            break;
                        default:
                            generalValidation(item, invalid_tag, self);
                            break;
                    }                    
                }
            });

            // IF DEBUGGING SET TO TRUE LOG IMPORTANT VALUES
            if(self.debugging === true){
                console.log('%cInputs / Outputs From : %cvalidate()', "color: #20c997", "font-weight:bold; #1B4F72");
                console.log('Form is set to : #' + form.attr('id'));
                console.log('Invalid Tag is set to : .' + invalid_tag);
                console.log('There are %c(' + Object.keys(self._validators).length + ') %cset validators', "font-weight:bold; #1B4F72", "font-weight:normal; #000000");
                $.each(self._validators, function(item){
                    console.log(item + ' validation is set to : ' + '%c' + self._validators[item], "font-weight:bold; font-size:1.1em;color: #20c997;");
                });
            }
        },

        // CHECK FORM FOR ERRORS
        checkForm: function(_invalid_tag, successCallback, errorCallback){
            // OPTIONALLY OVERRIDE THE DEFAULT SETTINGS 
            var self            =   this;
            var form            =   self._form;
            var invalid_tag     =   _invalid_tag    ||  self._invalid_tag;

            if(form.find('.' + invalid_tag).length == 0){
                successCallback(form);
            }else{
                errorCallback(form);
                setListeners();
            }
        },

    };

    // INITIALIZE ANY DEFAULT VARIABLES WHICH CAN BE OVERRIDDEN LATER
    validator.init    =   function(formid, debugging){
        var self            =   this;
        self.debugging      =   debugging || false;
        self._validators         = {
            phone: false,
            email: false,
            zip: false,
            general: false,
            password: false,
            password_match: false,
            checkbox: false
        };

        self._inputs;
        self._form               =    $(formid) || $('form');
        self._invalid_tag        =   'is-invalid';
        self._password_message   =   'Please enter a valid Password';
        self._match_message      =   'Passwords do not match';
        self._parent_wrapper     =   'invalid-feedback';
        self._checkbox_message   =   'Please accept the terms and conditions.';
        self.prevent_fields      =   self._form.find('[data-preventgroup]')
    
        self._validation_regex   =	{
            phone:      {regex: '\\([0-9]{3}\\)\\s[0-9]{3}-[0-9]{4}'},
            email:      {regex: '.*@{1}.*\.{1}.*'},
            zip:        {regex: '[0-9]{5}'},
            general:    {regex: '.+'},
        };

        // INITIALIZATION FUNCTIONS
        setListeners();
        disableKeypress(self);

        return self;
    };
    
    // LINK EXPOSED FUNCTIONS PROTOTYPE OBJECT TO INIT PROTOTYPAL CHAIN
    validator.init.prototype  =   validator.prototype;

    // SET GLOBAL OBJECT AND CREATE ALIAS
    global.validator    =   validator;
    
// WORKING FUNCTIONS

    // FUNCTION TO CONFIGURE VALIDATION METHODS
    function setInputs(formElement, self){
        self._inputs =   formElement.find(':input[type!="hidden"]'); 
    }

    function setPreventFields(collection){
        self.prevent_fields = collection;
    };

    function generalValidation(item, _invalid_tag, self){
        self._inputs.filter('[data-type="' + item + '"]:visible').each(function(){
            // GET LABEL TEXT
                var label = $(this).parents('.form-group').find('label').text().replace(':', '');
            // CREATE REGEX PATTERN
                var pattern	=	new RegExp(self._validation_regex[item].regex);
            // IF MATCH IS FOUND
                if(!pattern.test($(this).val())){
                // ADD ERROR MESSAGES & CLASSES
                    $(this).addClass(_invalid_tag)
                            .after('<div class="' + self._parent_wrapper + '">Please enter a valid ' + label.replace(/\*\s/,'') + '</div>');            
                }
            });
    };

    function checkPrevention(item, prevent_fields){
       var validationPrevented  =   false;
        if(typeof($(item).data('group')) !== 'undefined' && prevent_fields.length > 0){
            $.each(prevent_fields, function(index, element){;
                if($(element).data('preventgroup') === $(item).data('group')) {
                    if($(element).val() !== ''){
                        console.log($(element).val())
                        validationPrevented = true;
                    }
                }                
            });
        }
        return validationPrevented;
    };



// ------------------------------------------------------
// FUNCTIONS BELOW HERE STILL NEED TO BE REPAIRED TO WORK
// ------------------------------------------------------

    
    // CHECK PASSWORD FUNCTION 
        /**
         * @param {String} invalid_tag should be class used to mark element invalid
         */
        function validatePassword(data_type, invalid_tag, error_message){
            date_type = data_type || 'password';
            invalid_tag = invalid_tag || _invalid_tag;
            error_message = error_message || _password_message;

        // DATA-TYPE PASSWORD
            _inputs.filter('[data-type=".' + data_type + '"]').each(function(){
            // CREATE REGEX PATTERN
                /* Remove Feedback and Errors on Input Click/Focus/Change */
                $('form').on('click focus change', ':input', function() {
                    $(this).removeClass('is-valid is-invalid').parent().find('div.invalid-feedback').remove();
                    $(this).parent('.select-wrap').find('.select-box').removeClass('is-invalid');
                });
                var charUpper	=	new RegExp('[A-Z]+');
                var charLower	=	new RegExp('[a-z]+');
                var charNum 	=	new RegExp('[0-9]+');
                var charLimit	=	new RegExp('[^ ]{8}');
                var charSpecial	=	new RegExp('[!@#$%^&*]+');
    
                if(!charUpper.test($(this).val()) && !charLower.test($(this).val()) && !charNum.test($(this).val()) && !charLimit.test($(this).val()) && !charSpecial.test($(this).val())){
                // ADD ERROR MESSAGES & CLASSES
                    $(this).addClass(invalid_tag)
                            .after('<div class="' + _parent_wrapper + '">' + error_message + '</div>');
                }
            });
        }

        function setListeners(){
            /* Remove Feedback and Errors on Input Click/Focus/Change */
            $(':input:not([type="submit"])').on('click focus change', function() {
                var element =   this;
                if(typeof($(this).data('preventgroup')) !== 'undefined'){
                    var group   =   $(this).data('preventgroup');
                    element  =   $('[data-group="' + group + '"]');
                    console.log(element);
                }
                $(element).removeClass('is-invalid').parents('.form-group').find('div.invalid-feedback').remove();
                $(element).parents('.form-group').find('div.invalid-checkbox').remove();
                $(element).parent('.select-wrap').find('.select-box').removeClass('is-invalid');
            });
        }

        function disableKeypress(self){
            // DISABLE FORM SUBMIT ON ENTER KEYPRESS
            $(self._form).on('keyup keypress', function(event) {
                var keyCode = event.keyCode || event.which;
                if(keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });
        }
    
    // CHECK PASSWORD MATCH FUNCTION
        function passwordMatch(data_type, invalid_tag, error_message){
            data_type = data_type || 'password_match';
            invalid_tag  = invalid_tag || 'is-invalid';
            error_message  = error_message || 'Passwords do not match';
        // DATA-TYPE PASSWORD MATCH
            _inputs.filter('[data-type="' + data_type + '"]').each(function(){
                if($(this).val() !== $('[data-type="password"]').val()){
            // ADD ERROR MESSAGES & CLASSES
                    $(this).addClass(invalid_tag)
                            .after('<div class="' + _parent_wrapper + '">' + error_message + '</div>');
                }
            });
        }
    
    // CHECK IF CHECKBOX IS CHECKED
        function validateCheckbox(self, _invalid_tag){
            var checkboxElement = self._form.find(':input[data-type="checkbox"]:visible');
            checkboxElement.each(function(index, element){
                if(!checkPrevention(element, self.prevent_fields)){
                    if(!$(element).is(':checked')){
                    // ADD ERROR MESSAGES & CLASSES
                        console.log($(element).addClass(_invalid_tag).parents('.checkbox'));
                        $(element).addClass(_invalid_tag)
                                  .parents('.checkbox')
                                  .append('<div class="invalid-checkbox" style="display: block;">' + self._checkbox_message + '</div>'); 
                    }
                }
            });
        }
    
    
    })(window, $);
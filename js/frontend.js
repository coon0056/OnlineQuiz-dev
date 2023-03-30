jQuery(document).ready(function(){
    jQuery("#questions li").slice(1).hide();
    jQuery('#pagination').pagination({

        items: jQuery('#questionTotal').val(),

        // Items allowed on a single page
        itemsOnPage: 1, 
        onPageClick: function (noofele) {
            jQuery("#questions li").hide()
                .slice(1*(noofele-1), 1+ 1*(noofele - 1)).show();
        }
    });

    jQuery('#user_question_submit').click(function(e){
        var flag = false;

        jQuery("#questions li").each(function() {
            if(jQuery(this).find('input:invalid,select:invalid').length > 0){
                flag = true;
                jQuery('#modalMessage').text("Question "+ (jQuery(this).data('num') + 1) + 
                        " is unanswered. Please fill out all questions before submitting the quiz.");
                
                return false; //breaks out of jQuery each()
            } 
            //If the question has checkboxes check if one is checked
            else if(jQuery(this).has('input:checkbox').length != 0) {
                if(jQuery(this).has('input[type=checkbox]:checked').length == 0) {
                    flag = true;
                    jQuery('#modalMessage').text("Question "+ (jQuery(this).data('num') + 1) + 
                        " is unanswered. Please fill out all questions before submitting the quiz.");
                }
                return false; //breaks out of jQuery each()
            }
        });

        if(flag == true){
            e.preventDefault();
            jQuery('#modal').show();
    
            var modal = document.getElementById("modal");
            modal.style.display = "block";
            jQuery('#close').click(function(){
                modal.style.display = "none";
            });
    
            window.onclick = function(e){
                if (e.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    });

});
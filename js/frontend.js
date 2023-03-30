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
        //console.log(jQuery("#questions").find('input:invalid,select:invalid'));

        jQuery("#question").each(function(index, element) {
            console.log(jQuery(element));
            e.preventDefault();
            if(jQuery(element).find('input:invalid,select:invalid').length > 0){
                e.preventDefault();
    
                jQuery('#modalMessage').text("Question "+ (jQuery("#questions").find('input:invalid,select:invalid').closest('li').data('num') + 1) + 
                    " is unanswered. Please fill out all questions before submitting the quiz.")
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
            } else {
                console.log("fail");
            }
        });

        // if(jQuery("#questions").find('input:invalid,select:invalid').length > 0){
        //     e.preventDefault();

        //     jQuery('#modalMessage').text("Question "+ (jQuery("#questions").find('input:invalid,select:invalid').closest('li').data('num') + 1) + 
        //         " is unanswered. Please fill out all questions before submitting the quiz.")
        //     jQuery('#modal').show();

        //     var modal = document.getElementById("modal");
        //     modal.style.display = "block";
        //     jQuery('#close').click(function(){
        //         modal.style.display = "none";
        //     });

        //     window.onclick = function(e){
        //         if (e.target == modal) {
        //             modal.style.display = "none";
        //         }
        //     }
        // }
    });

});
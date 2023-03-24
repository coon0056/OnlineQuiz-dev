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

    //TODO
    //jQuery('#user_question_submit').click(function(e){
        // if question is unanswered
        //if(jQuery("#question").find('input:invalid')){
            //e.preventDefault();
            //console.log(jQuery("#question").find('input:invalid').attr('id'));
            //console.log(jQuery("#question").find('input:invalid').closest("div").attr('name'));
            //alert(jQuery("input").find(":invalid").first());
        //}
    //});
});
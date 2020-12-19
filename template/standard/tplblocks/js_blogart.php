<script type="text/javascript">

$(document).ready(function(){
    /* The following code is executed once the DOM is loaded */
    
    /* This flag will prevent multiple comment submits: */
    var working = false;
    
    /* Listening for the submit event of the form: */
    $('.jak-blogform').submit(function(e){

        e.preventDefault();
        if(working) return false;
        
        working = true;
        var jakform = $(this);
        var button = $(this).find('.jak-submit');
        var answerid = $('#comanswerid').val();
        $(this).find('input, textarea').removeClass("is-invalid");
        $(this).find('input, textarea').removeClass("is-valid");
        
        $(button).html(ls.ls_submitwait);
        
        // Now this is ajax
        var data = $(this).serializeArray(); // convert form to array
        data.push({name: "jakajax", value: "yes"});
        
        /* Sending the form fields to any post request: */
        $.post('<?php echo $_SERVER['REQUEST_URI'];?>', $.param(data), function(msg) {
            
            working = false;
            $(button).html(ls.ls_submit);
            
            if (msg.status == 1) {
                
                $('#comments-blank').fadeOut();
                if (answerid) {
                    $('#insertPost_'+answerid).html(msg.html).fadeIn();
                    // Check if we have an id
                    $('#comanswerid').val(0);
                    $('.jak-creply .reply').removeClass("btn-success reply").addClass("btn-primary");
                } else {
                    $('#insertPost').html(msg.html).fadeIn();
                }
                $('#bmessage').val('');
                
                var countSpan = $('#cComT').text();
                var newSpan = parseInt(countSpan) + 1;
                
                $('#cComT').html(newSpan);
                $(jakform)[0].reset();
                
                // Fade out the form
                $(button).fadeOut().delay('500');

            } else if (msg.status == 2) {

                $('.jak-epost').removeClass("btn-success edit").addClass("btn-secondary");

                $('#msgid_'+msg.id).html(msg.html);
                $('#bmessage').val('');
                
            } else {
                /*
                /   If there were errors, loop through the
                /   msg.errors object and display them on the page 
                /*/
                
                $('#bmessage').addClass("is-invalid");
            }
        }, 'json');

    });
    
});

// get the vote button
var votelems = document.getElementsByClassName('jak-cvote');
// Get the reply button
var replyelems = document.getElementsByClassName('jak-creply');
// Get the edit buttons
var epostelems = document.getElementsByClassName('jak-epost');

document.addEventListener('DOMContentLoaded', function() {

    for (var i = 0; i < votelems.length; i++) {
        votelems[i].addEventListener('click', jak_record_votes, false);
    }
    
    for (var i = 0; i < replyelems.length; i++) {
        replyelems[i].addEventListener('click', jak_set_replyid, false);
    }

    for (var i = 0; i < epostelems.length; i++) {
        epostelems[i].addEventListener('click', jak_set_editpost, false);
    }

});

function jak_record_votes() {
    
    // Do we have a vote already
    voted = false;
    if (this.classList)
      voted = this.classList.contains("voted");
    else
      voted = new RegExp('(^| )' + className + '( |$)', 'gi').test(this.className);
      
    if (!voted) {
    
        // Which container to update
        vc = document.getElementById("jak-cvotec"+this.dataset.id);
        
        // tak the vote status
        vs = this.dataset.cvote;
        
        // Curren Vote
        cv = vc.textContent;
        
        // finally add the class so we can only vote once
        if (this.classList)
          this.classList.add("voted");
        else
          this.className += ' ' + className;
            
        var request = new XMLHttpRequest();
        request.open('GET', '<?php echo BASE_URL;?>include/comment_vote.php?vid='+this.dataset.id+'&vote='+this.dataset.cvote, true);
            
            request.onload = function() {
              if (request.status >= 200 && request.status < 400) {
                // Success!
                var data = JSON.parse(request.responseText);
                
                if (data.status == 1) {
                    
                    if (cv == "0" && vs == "down") {
                    
                        jak_remove_add_class(vc, "label-secondary", "label-danger");
                    
                    } else if (cv == "0" && vs == "up") {
                    
                        jak_remove_add_class(vc, "label-secondary", "label-success");
                        
                    } else if (cv == "-1" && vs == "up") {
                    
                        jak_remove_add_class(vc, "label-danger", "label-secondary");
                        
                    } else if (cv == "1" && vs == "down") {
                    
                        jak_remove_add_class(vc, "label-success", "label-secondary");
                        
                    } else {
                        // Nothing to change
                    }
                    
                    // Update the number
                    if (vs == "up") {
                        vc.textContent = parseInt(cv) + 1;
                    } else {
                        vc.textContent -= 1;
                    }

                    $.notify({icon: 'fa fa-check-square-o', message: '<?php echo $jkl['hd71'];?>'}, {type: 'success', animate: {
                        enter: 'animated fadeInDown',
                        exit: 'animated fadeOutUp'
                    }});
                                
                }
                
              } else {
                // We reached our target server, but it returned an error
                $.notify({icon: 'fa fa-exclamation-triangle', message: '<?php echo $jkl['hd72'];?>'}, {type: 'danger', animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                }});
            
              }
            };
            
            request.onerror = function() {
              // There was a connection error of some sort
            };
            
            request.send();
        
    } else {
        // We could output something.
        return false;
    }
};

function jak_set_replyid() {

    // Check if we have an id
    ri = document.getElementById("comanswerid");

    [].forEach.call(replyelems, function(el) {
        
        // remove the success button
        jak_remove_add_class(el, "btn-success", "btn-primary");
        
        if (el.classList)
          el.classList.remove("reply");
        else
          el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    
    });
    
    if (ri.value == this.dataset.id) {
    
        // Add the danger button
        jak_remove_add_class(this, "btn-success", "btn-primary");
        
        ri.value = 0;
    
    } else {
    
        // Now set the id
        ri.value = this.dataset.id;
        
        // Add the danger button
        jak_remove_add_class(this, "btn-primary", "btn-success");
        
        if (this.classList)
          this.classList.add("reply");
        else
          this.className += ' ' + className;
          
        // Focuse the textarea
        document.getElementById("bmessage").focus();
    }

}

function jak_set_editpost() {

    // Check if we have an id
    ri = document.getElementById("editpostid");

    [].forEach.call(epostelems, function(el) {
        
        // remove the success button
        jak_remove_add_class(el, "btn-success", "btn-secondary");
        
        if (el.classList)
          el.classList.remove("edit");
        else
          el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    
    });
    
    if (ri.value == this.dataset.id) {
    
        // Add the danger button
        jak_remove_add_class(this, "btn-success", "btn-secondary");
        
        ri.value = 0;

        document.getElementById("bmessage").value = '';
    
    } else {
    
        // Now set the id
        ri.value = this.dataset.id;
        
        // Add the danger button
        jak_remove_add_class(this, "btn-secondary", "btn-success");
        
        if (this.classList)
          this.classList.add("edit");
        else
          this.className += ' ' + className;
          
        // Focuse the textarea
        document.getElementById("bmessage").value = this.dataset.msg;
        document.getElementById("bmessage").focus();
    }

}

function jak_remove_add_class(el, classremove, classadd) {
    
    if (el.classList)
      el.classList.remove(classremove);
    else
      el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
      
    if (el.classList)
      el.classList.add(classadd);
    else
      el.className += ' ' + className;

}
</script>
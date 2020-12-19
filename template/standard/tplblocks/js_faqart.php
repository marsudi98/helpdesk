<script type="text/javascript">

// get the vote button
var votelems = document.getElementsByClassName('jak-cvote');

document.addEventListener('DOMContentLoaded', function() {

    for (var i = 0; i < votelems.length; i++) {
        votelems[i].addEventListener('click', jak_record_votes, false);
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
        request.open('GET', '<?php echo BASE_URL;?>include/faq_vote.php?vid='+this.dataset.id+'&vote='+this.dataset.cvote, true);
            
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
                                
                    } else {
                    // We reached our target server, but it returned an error
                    $.notify({icon: 'fa fa-exclamation-triangle', message: '<?php echo $jkl['hd72'];?>'}, {type: 'danger', animate: {
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
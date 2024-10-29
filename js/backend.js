jQuery(document).ready(function($) {
  
  var OUTPUT = "#p-ac-list-field";
  
  $(OUTPUT).sortable({   
    placeholder: "p-ac-list-element-placeholder"
  }); 
  
  $("#p-ac-list-target .p-ac-list-element").draggable({
    connectToSortable: OUTPUT,
    helper: "clone",
    revert: "invalid"
  });
  
  $(document).on('click',OUTPUT + ' .p-ac-list-element-header',function() {
    $(this).parent().find('.p-ac-list-element-content').toggle(200);
  });
  
  $(document).on('submit','#p-ac-new-form-submit',function(e){
    
    var fields = [];
    $('#p-ac-list-field').find('.p-ac-list-element').each(function(){
      fields.push(ReadForm($(this)));
    });
    
    var data = {
      title:    $('#p-ac-list-name').val(),
      field:    fields,
      text:     ReadForm($('#p-ac-list-text')),
      setting:  ReadForm($('#p-ac-list-setting'))
    };
    
    pac_ajax({
      data: {
        wp_nonce: $('#wp_nonce').val(),
        action: 'pac_form',
        args: data
      },
      dataType: 'json',
      done: function(d) {
        if (typeof d == 'object') {
          if(d.hasOwnProperty('error')) {
            showError(d.error.msg);
          } else {
            showSuccess(d.msg);
          }
          if (d.hasOwnProperty('location')) {
            location.href = d.location;
          }
        } else {
          showError('wp api sucks');
        }
      }
    });
    
    $(document).scrollTop(0);
    
    return false;
  });
  
  $(document).on('submit','#p-ac-edit-form-submit',function(e){
    
    var fields = [];
    $('#p-ac-list-field').find('.p-ac-list-element').each(function(){
      fields.push(ReadForm($(this)));
    });
    
    var data = {
      id:       $('#id').val(),
      title:    $('#p-ac-list-name').val(),
      field:    fields,
      text:     ReadForm($('#p-ac-list-text')),
      setting:  ReadForm($('#p-ac-list-setting'))
    };
    
    pac_ajax({
      data: {
        wp_nonce: $('#wp_nonce').val(),
        action: 'pac_form',
        args: data
      },
      dataType: 'json',
      done: function(d) {
        if (typeof d == 'object') {
          if(d.hasOwnProperty('error')) {
            showError(d.error.msg);
          } else {
            showSuccess(d.msg);
          }
          if (d.hasOwnProperty('location')) {
            location.href = d.location;
          }
        } else {
          showError('wp api sucks');
        }
      }
    });
    
    $(document).scrollTop(0);
    
    return false;
  });
  
  function ReadForm(jQ) {
    var temp = {};
    jQ.find('input,select,textarea').each(function(){
      var $this = $(this),
          name = $this.prop('name');
      if (name.length > 0) {
        switch($this.prop('type')) {
          case 'checkbox':
            if ($this.is(':checked')) {
              temp[name] = true;
            } else {
              temp[name] = false;
            }
            break;
          default:
            temp[name] = $this.val();
        }
      }
    });
    return temp;
  }
  
  function pac_ajax(args) {
    
    if(arguments.length === 0) args = {};
    
    args = $.extend({
      always      : function() {$('#p-ac-loader').remove();},
      before      : function() {$('body').append('<div id="p-ac-loader"></div>');},
      cache       : false,
      contentType : 'application/x-www-form-urlencoded; charset=UTF-8',
      data        : {},
      dataType    : "",
      done        : function() {},
      fail        : function (jqXHR, textStatus, errorThrown) {
        log(jqXHR);
        log(textStatus);
        log(errorThrown);
      },
      processData : true,
      //progress    : false,
      timeout     : 0,
      type        : "POST",
      url         : WP_ADMIN_AJAX_URL
    },args);
    
    return $.ajax({
      beforeSend  : args.before,
      cache       : args.cache,
      contentType : args.contentType,
      data        : $.extend({dataType:args.dataType},args.data),
      dataType    : args.dataType,
      processData : args.processData,
      //progress    : args.progress,
      timeout     : args.timeout,
      type        : args.type,
      url         : args.url,
    }).done(args.done).fail(args.fail).always(args.always);
  }
  
  function showSuccess(msg) {
    $('#p-ac-placeholder-for-message').html('<div class="updated"><p><strong>'+msg+'</strong></p></div>').show();
  }
  
  function showError(msg) {
    $('#p-ac-placeholder-for-message').html('<div class="error"><p><strong>'+msg+'</strong></p></div>').show();
  }
  
});

function log(args,line) {
  switch (arguments.length) {
    case 1:
      console.log(args);
      break;
    case 2:
      console.log(line);
      console.log(args);
      break;
  }
}
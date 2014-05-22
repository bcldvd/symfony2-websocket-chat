var _room = 'chat/general';
var nickname = 'Guest';

$(document).ready(function() {

  // Ask for our name and calls client 'Anonymous' if he entered nothing
  bootbox.prompt("What is your name?", function(result) {
    if (result === null) {
      nickname = 'Anonymous';
    } else {
      nickname = result;
    }

    // Set the newly appointed nickname
    $('#nickname').html(nickname);

    // Connect to Clank
    var network = Clank.connect(_CLANK_URI);

    network.on("socket/connect", function(session){
        setNickname(session);
        subscribeToRoom(session, _room);
        $('#panelChat .panel-heading').html('You are connected to the General channel');
        console.log('Connected to : '+_room);
        bindUi(session);
    });

    network.on("socket/disconnect", function(session){
        $('#panelChat .panel-heading').html('You are not connected to any channel');
        console.log('disconnected to : '+_room);
        unbindUi();
    });

  });

});

/**
 * Below is the Clank relevant functions, the rest of this file is UI Bindings etc.
 */

function setNickname(session){
  session.call("chat/change_nickname", {nickname: nickname});
}

function subscribeToRoom(session, room) {
    session.subscribe(_room, function(uri, payload){
        appendChat(payload.from, payload.msg, payload.system);
    });
}


function publishChat(session) {
    if (!_room)
        return;

    var msg = $("#chat-input").val();
    $("#chat-input").val("");

    session.publish(_room, msg);
}


function appendChat(from, msg, system) {
    if(system === true){
      $(".panel-body").append('<div class="alert alert-info">' + msg + '</div>');
    }else{
      $(".panel-body").append("<div><span>"+from+"</span>: " + msg + "</div>");
    }
    $(".panel-body").scrollTop($('.panel-body').get(0).scrollHeight);
}


function bindUi(session) {
    $("#send-chat").bind("click", function(e){
        e.preventDefault();
        publishChat(session)
    });

    $("#chat-input").bind("keypress", function(e){
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code == 13) { //Enter keycode
            publishChat(session);
        }
    });
}

// attempt to clean up on disconnect.
function unbindUi() {
    $("#join-chat").unbind();
    $("#chatroom").unbind();

    $("#send-chat").unbind();
    $("#chat-input").unbind();

    $("#nickname > input").unbind();
    $("#nickname > a").unbind();
}

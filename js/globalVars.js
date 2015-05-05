var getUrlDir = function() { return document.URL.substr(0,document.URL.lastIndexOf('/')); };
var participantImgRoot = "img/Participant";
var fieldParticipantImage = function(path) {
    var ret = $('<img>').toggleClass('participantAvatar');
    path && ret.attr('src', participantImgRoot + '/' + path);
    return ret;
};
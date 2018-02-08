var Home = function(){

    var _init = function(){
        $('.hero .icon-play').click(initHeroVideo);
    };

    var initHeroVideo = function(){
        var $hero = $('.hero');
        $hero.find('>*').hide();
        $hero.find('iframe').show().attr('src', $hero.find('iframe').data('video-url'));
    };

    _init();
};

$(function(){ new Home(); })
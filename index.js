var fs = require('hexo-fs'),
	util = require('hexo-util'),
	path = require('path'),
	XMLHttpRequest = require('xmlhttprequest').XMLHttpRequest,
	counter = 0,
	srcDir = path.dirname(require.resolve('aplayer')),
	scriptDir = 'assets/js/',
	aplayerScript = 'APlayer.min.js',
	registers = [
		[aplayerScript, scriptDir + aplayerScript, path.join(srcDir, aplayerScript)]
	];

for (var i = 0; i < registers.length; ++i) {
	(function (i) {
		var register = registers[i], regName = register[0],
			pubPath = register[1], srcPath = register[2];
		hexo.extend.generator.register(regName, function(locals) {
			return {
				path: pubPath,
				data: function() {
					return fs.createReadStream(srcPath);
				}
			};
		});
	})(i);
}

hexo.extend.tag.register('aplayerlist', function(args) {
        var albumlist_id = args[0], narrow = false, autoplay = false, width = '',
        id = 'aplayer' + (counter++), raw = '', content = '';
    // Parse optional arguments
    if (args.length > 1) {
        var options = args.slice(1);
        narrow = options.indexOf('narrow') < 0 ? false : true;
        autoplay = options.indexOf('autoplay') < 0 ? false : true;
        for (var i = 0; i < options.length; i++) {
            var option = options[i];
            width = option.indexOf('width:') == 0 ? option + ';' : width;
        }
    }
    width = narrow ? '' : width;
    var album;
    var req = new XMLHttpRequest();
        req.open('GET', "http://bgm.iminyao.com/wechat/hexo_playlist.php?id=" + albumlist_id, false);
        req.send();
        if(req.status === 200) {
            album = req.responseText;
        }
        raw = '<div id="'+ id + '" class="aplayer" style="margin-bottom: 20px;display:inline-block;'+ width + '">';
        raw +=
        '</div><script>var '+ id + ' = new APlayer({'+
                'element: document.getElementById("'+ id +'"),' +
                'narrow: ' + (narrow ? 'true' : 'false') + ',' +
                'autoplay: ' + (autoplay ? 'true' : 'false') + ',' +
                'music : ' + album +
            '});' +
        id + '.init();</script>';
    return raw;
});
hexo.extend.tag.register('aplayer', function(args) {
        var narrow = false, autoplay = false, width = '',
        id = 'aplayer' + (counter++), raw = '', content = '';
    if (args.length > 1) {
        var options = args.slice(1);
        narrow = options.indexOf('narrow') < 0 ? false : true;
        autoplay = options.indexOf('autoplay') < 0 ? false : true;
        for (var i = 0; i < options.length; i++) {
            var option = options[i];
            width = option.indexOf('width:') == 0 ? option + ';' : width;
        }
    }
    width = narrow ? '' : width;
    var album = [];
    var data = eval(args[0]);
    data.forEach(function (song) {
        var song_info = {
            title: song[0],
            author: song[1],
            url: song[2],
            pic: song[3]
        };
    album.push(song_info);
    });
    raw = '<div id="'+ id + '" class="aplayer" style="margin-bottom: 20px;'+ width + '">';
    raw +=
        '</div><script>var '+ id + ' = new APlayer({'+
                'element: document.getElementById("'+ id +'"),' +
                'narrow: ' + (narrow ? 'true' : 'false') + ',' +
                'autoplay: ' + (autoplay ? 'true' : 'false') + ',' +
                'music : ' + JSON.stringify(album) +
            '});' +
        id + '.init();</script>';
    return raw;
});

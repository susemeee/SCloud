if(window.File && window.FileReader && window.FileList && window.Blob){
	//.uploadable div and JQuery draggable element has weird behavior if we strech it to 100% height
	//so we assign holder to body instead
	var holder = $('body');
	holder.bind('drop', drop);
	holder.bind({
		dragenter : cancel, dragover : cancel
	});

	//we need no filereader to make an AJAX call
	//var reader = new FileReader();

	var start = 0;
	var aa = [];
	function drop(e){
		// stops the browser from redirecting off to the image.
		if (e.preventDefault) { e.preventDefault(); } 

		var files = e.originalEvent.dataTransfer.files;
		for (i = 0; i < files.length; i++) {
			var file = files[i];
			//default filetype
			if(file.type.length < 1) file.type = "application/octet-stream";


			var uid = ++start;
			//console.log(file.name+"|"+file.size+"|"+file.type);
			//reader.readAsDataURL(file);
			var fd = new FormData();
        	fd.append('file', file);
			//we need to adjust file's initial position slightly, if uploaded
			//(because pageX and left attribute of CSS doesn't match)
        	fd.append('x', e.originalEvent.pageX-45);
        	fd.append('y', e.originalEvent.pageY-65);

			//creating "load progress" canvas
    	    createProgress(uid, e.originalEvent.pageX-45, e.originalEvent.pageY-65);	
        	//starting AJAX call
	        sendFileToServer(uid, fd, status);
		}
		return false;
	}
	//called when load cancelled
	function cancel(e){
		if (e.preventDefault) { e.preventDefault(); }
		return false;
	}

	function sendFileToServer(uid, formData)
	{
	    var ajax = $.ajax({
	            xhr: function() {
	            var xhrobj = $.ajaxSettings.xhr();
	            if (xhrobj.upload) {
	                    xhrobj.upload.addEventListener('progress', function(event) {
	                        var percent = 0;
	                        var position = event.loaded || event.position;
	                        var total = event.total;
	                        if (event.lengthComputable) {
	                        	console.log(uid);
	                        	//raw byte status needed
	                        	var p = Math.round(position/1024 * 1000) / 1000;
	                        	var t = Math.round(total/1024 * 1000) / 1000;
								$('#u'+uid).text(p+' kB / '+t+' kB');
								//
	                            percent = Math.ceil(position / total * 100);
	                        }
	                        //Set progress
	                        setProgress(uid, percent);
	                    }, false);
	                }
	            return xhrobj;
	        },
	        url: "./_core/?action=upload",
	        type: "POST",
	        contentType: false,
	        processData: false,
	        cache: false,
	        data: formData,
	        success: function(result){
	        	delete aa[uid];
	            setProgress(uid, 100);
	 			$('#progress'+uid).remove();
	 			$('#u'+uid).remove();
	 			display_result(result);
	        },
	       	error: function(result){
	       		delete aa[uid];
	       		display_result(result);
	       	}
	    });
		aa[uid] = ajax;
	}
	function display_result(data){
		// console.log(data);
		if(data.indexOf('<div') > -1){
			var file = $(data);
			//bug?
			make_floatable(file, true);
			$('#file-container').append(file);
		}else{
			var error = $("<div class='alert alert-danger hidden'>");
			error.text(data);
			$('#file-container').after(error);
			setTimeout(function(){
				error.fadeOut('1000', function() {
					error.remove();
				});		
			}, 5000);

		}
	}

	function createProgress(uid, x, y){
		var canvas = $("<canvas width='140' height='140'></canvas>");
		canvas.attr('id', 'progress'+uid);
		canvas.css('top', y-30);
		canvas.css('left', x-30);
		canvas.css('position', 'absolute');
		canvas.css('background-color', 'rgba(0,0,0,0)');
		$('#file-container').append(canvas);
		var progress = $("<div class='file-tooltip uploading'></div>");
		progress.attr('id', 'u'+uid);
		progress.append("<div class='btn btn-danger'>cancel</div>");
		progress.css('top', y+120);	//30+90
		progress.css('left', x-60);
		progress.find('.btn').click(function(){
			console.log(uid+" aborted");
			aa[uid].abort();
	 		$('#progress'+uid).remove();
	 		$('#u'+uid).remove();
		})
		$('#file-container').append(progress);
	}

	function setProgress(uid, percent){
		console.log(percent/100*360);
		var radian = (percent/100*Math.PI*2);
		var ctx = document.getElementById('progress'+uid).getContext('2d');
		//clear the rect first
		ctx.clearRect(0,0,140,140);
		//draw outer PI
		ctx.lineWidth=15;
		ctx.beginPath();
		ctx.arc(70,70,60,0,radian);
		ctx.strokeStyle = "#1abc9c";
		ctx.stroke();
		//draw inner circle - always full
		ctx.beginPath();	
		ctx.arc(70,70,45,0,Math.PI*2);	
		ctx.fillStyle = 'white';
		ctx.fill();
		//draw text
		ctx.font="26px Lato bold";
		ctx.textAlign = 'center';
		ctx.fillStyle = 'black';
		ctx.fillText(percent+"%",70,78);
	}
}else {
	alert('The File APIs are not fully supported in this browser.');
}


$(document).ready(function(){
	make_floatable_all(true);
});

function make_floatable_all(first){
	var files = $('file');
	files.each(function(){
		make_floatable($(this), first);
	});
}

function unmake_floatable_all(){
	var files = $('file');
	files.each(function(){
		$(this).unbind('click');
	});
}

function make_floatable(file, first){
	//making file draggable
	file.draggable({
		containment: "window",
		stop: dragended
	});
	file.click(function(){window.location.href = "./_core/?action=download&fid="+file.attr('id');});
	
	//clicking tooltip won't proceed to download
	file.find('.file-tooltip').click(function(e){e.stopPropagation();})

	//file->tooltip retains tooltip, not vice versa
	file.mouseenter(function(e){
		if(e.target === this){
			file.find('.file-tooltip').css('display', 'block');
		}
	}).mouseleave(function(e){
		setTimeout(function(){
			file.find('.file-tooltip').css('display', 'none');
		}, 200);
	});

	if(first){
		//assign event to child element
		file.find('.link').click(link_file);
		file.find('.del').click(delete_file);
	}
}

function dragended( event, ui ){
	var file = $(this).attr('id');
	var x = $(this).css('left').replace("px", "");
	var y = $(this).css('top').replace("px", "");
	
	$.ajax({
		type: "GET",
		url: "./_core/",
		data: "action=updatepos&file="+file+"&x="+x+"&y="+y,
		success: function(){}
	});
}

function delete_file(e){
	//to not calling parent link(download the file)
	e.stopPropagation();

	//revert to original state
	$(this).mouseleave(function(){
		$(this).text('del');
		$(this).click(delete_file);
	});

	$(this).text('sure?');
	$(this).click(function(){
		var file = $(this).parentsUntil('#file-container').slice(-1);
		var id = file.attr('id');
		$.ajax({
			url: "./_core/?action=delete&fid="+id,
			success: function(result){ file.fadeOut(200, function(){file.remove();});}
		});
	});
}

function link_file(e){
	e.stopPropagation();
	$.ajax({
		type: "GET",
		url: "./_core/",
		data: "actionlink&file="+file,
		success: function(result){
			
		}
	});
}
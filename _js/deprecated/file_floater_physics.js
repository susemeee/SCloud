var b2Vec2 = Box2D.Common.Math.b2Vec2,
	b2BodyDef = Box2D.Dynamics.b2BodyDef,
	b2AABB = Box2D.Collision.b2AABB,
	b2Body = Box2D.Dynamics.b2Body,
	b2FixtureDef = Box2D.Dynamics.b2FixtureDef,
	b2Fixture = Box2D.Dynamics.b2Fixture,
	b2World = Box2D.Dynamics.b2World,
	b2MassData = Box2D.Collision.Shapes.b2MassData,
	b2PolygonShape = Box2D.Collision.Shapes.b2PolygonShape,
	b2CircleShape = Box2D.Collision.Shapes.b2CircleShape,
	b2DebugDraw = Box2D.Dynamics.b2DebugDraw,
	b2MouseJointDef =  Box2D.Dynamics.Joints.b2MouseJointDef,
	b2EdgeShape = Box2D.Collision.Shapes.b2EdgeShape;

var world;
var SCALE = 30;
var D2R = Math.PI / 180;
var R2D = 180 / Math.PI;
var PI2 = Math.PI * 2;
var interval;

//Cache the canvas DOM reference
var canvas;
var debug = false;

function init() {

	world = new b2World(
	new b2Vec2(0, 0) //gravity
	, true //allow sleep
	);

	//setup debug draw
	var debugDraw = new b2DebugDraw();
	canvas = $("#canvas");
	debugDraw.SetSprite(canvas[0].getContext("2d"));
	debugDraw.SetDrawScale(SCALE);
	debugDraw.SetFillAlpha(0.3);
	debugDraw.SetLineThickness(1.0);
	debugDraw.SetFlags(b2DebugDraw.e_shapeBit | b2DebugDraw.e_jointBit);
	world.SetDebugDraw(debugDraw);

	//Create DOM Objects
	createDOMObjects();

	//Make sure that the screen canvas for debug drawing matches the window size
	resizeHandler();
	$(window).bind('resize', resizeHandler);

	//Simple solution; reload to reset
	$("#btn-reset").click(function() {
		document.location.reload();
	});

	$("#debug").click(function () {
		if ($("#debug:checked").val()) {
			debug = true;
		} else {
			debug = false;
			canvas.width = canvas.width;
		}
		$("article").animate({opacity:debug ? 0.2 : 1},1000);
	})

	$("#removeText").click(function() {
		$(".panel p").hide()
	});

	//Create the static ground
	var w = $(window).width(); 
	var h = $(window).height();

	createBox(0,h,w,5, true);	//bottom
	createBox(0,0,5,h, true);	//left
	createBox(w,0,5,h, true);	//right
	createBox(0,0,w,5, true);	//top

	//Do one animation interation and start animating
	interval = setInterval(update,1000/60);
	update();
}

function createDOMObjects() {
	//iterate all 'file' elements and create them in the Box2D system
	$("#file-container file").each(function (a,b) {
		var domObj = $(b);
		var domPos = $(b).position();
		var width = domObj.width() / 2 ;
		var height = domObj.height() / 2;
		
        var x = (domPos.left) + width;
        var y = (domPos.top) + height;
        var body = createBox(x,y,width,height);
		body.m_userData = {domObj:domObj, width:width, height:height};
		
		//Reset DOM object position for use with CSS3 positioning
		domObj.css({'left':'0px', 'top':'0px'});
	});
}

function createBox(x,y,width,height, static) {
	var bodyDef = new b2BodyDef;
	bodyDef.type = static ? b2Body.b2_staticBody : b2Body.b2_dynamicBody;
	bodyDef.position.x = x / SCALE;
	bodyDef.position.y = y / SCALE

	var fixDef = new b2FixtureDef;
 	fixDef.density = 1;
 	fixDef.friction = 2;
 	fixDef.restitution = 0.25;

	fixDef.shape = new b2PolygonShape;
	fixDef.shape.SetAsBox(width / SCALE, height / SCALE);
	return world.CreateBody(bodyDef).CreateFixture(fixDef);
}

//Animate DOM objects
function drawDOMObjects() {
	var i = 0;
	for (var b = world.m_bodyList; b; b = b.m_next) {
         for (var f = b.m_fixtureList; f; f = f.m_next) {
				if (f.m_userData) {
					//Retrieve positions and rotations from the Box2d world
					var x = Math.floor((f.m_body.GetWorldCenter().x * SCALE) - f.m_userData.width);
					var y = Math.floor((f.m_body.GetWorldCenter().y * SCALE) - f.m_userData.height);

					//CSS3 transform does not like negative values or infitate decimals
					var r = Math.round(((f.m_body.m_sweep.a + PI2) % PI2) * R2D * 100) / 100;

					var css = {'-webkit-transform':'translate(' + x + 'px,' + y + 'px) rotate(' + r  + 'deg)', '-moz-transform':'translate(' + x + 'px,' + y + 'px) rotate(' + r  + 'deg)', '-ms-transform':'translate(' + x + 'px,' + y + 'px) rotate(' + r  + 'deg)'  , '-o-transform':'translate(' + x + 'px,' + y + 'px) rotate(' + r  + 'deg)', 'transform':'translate(' + x + 'px,' + y + 'px) rotate(' + r  + 'deg)'};

					f.m_userData.domObj.css(css);
				}
         }
      }
};

function update() {
	updateMouseDrag();

	world.Step(
	1 / 60 //frame-rate
	, 10 //velocity iterations
	, 10 //position iterations
	);

	if (debug) {
		world.DrawDebugData();
	}

	drawDOMObjects();

	world.ClearForces();
}

//Keep the canvas size correct for debug drawing
function resizeHandler() {
	canvas.attr('width', $(window).width());
	canvas.attr('height', $(window).height());
}
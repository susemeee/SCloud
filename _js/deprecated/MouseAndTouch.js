/* 
   MouseAndTouch by Pål Smitt-Amundsen 
   http://paal.org/blog
*/

function MouseAndTouch(dom, down, up, move) {
   var canvas = dom;
   var mouseX, mouseY, startX, startY;
   var isDown = false;


   canvas.addEventListener("mousedown", mouseDownHandler, true);
   canvas.addEventListener("touchstart", touchDownHandler, true);

   //When drawing the "road" get mouse or touch positions
   function mouseMoveHandler(e) {
      updateFromEvent(e);
      move(mouseX, mouseY);
   }

   function updateFromEvent(e) {
      e.preventDefault();
      var touch = e.originalEvent;
      if (touch && touch.touches && touch.touches.length == 1) {
         //Prevent the default action for the touch event; scrolling
         touch.preventDefault();
         mouseX = touch.touches[0].pageX;
         mouseY = touch.touches[0].pageY;
      } else {
         mouseX = e.pageX;
         mouseY = e.pageY;
      }
   }

   function mouseUpHandler(e) {
      canvas.addEventListener("mousedown", mouseDownHandler, true);
      canvas.removeEventListener("mousemove", mouseMoveHandler, true);
      isDown = false;
      updateFromEvent(e);
      up(mouseX, mouseY);
   }

   function touchUpHandler(e) {
      canvas.addEventListener("touchstart", touchDownHandler, true);
      canvas.removeEventListener("touchmove", mouseMoveHandler, true);
      isDown = false;
      updateFromEvent(e);
      up(mouseX, mouseY);
   }

   function mouseDownHandler(e) {
      canvas.removeEventListener("mousedown", mouseDownHandler, true);
      canvas.addEventListener("mouseup", mouseUpHandler, true);
      canvas.addEventListener("mousemove", mouseMoveHandler, true);
      isDown = true;
      updateFromEvent(e);
      down(mouseX, mouseY);
   }

   function touchDownHandler(e) {
      canvas.removeEventListener("touchstart", touchDownHandler, true);
      canvas.addEventListener("touchend", touchUpHandler, true);
      canvas.addEventListener("touchmove", mouseMoveHandler, true);
      isDown = true;
      updateFromEvent(e);
      down(mouseX, mouseY);
   }

   var ret = {};
   ret.mouseX = function () {
      return mouseX;
   }
   ret.mouseY = function () {
      return mouseY;
   }
   ret.isDown = function () {
      return isDown
   }

   return ret;
}

         var mouseX, mouseY, mousePVec, isMouseDown, selectedBody, mouseJoint;
         var canvasPosition = getElementPosition(document.getElementById("canvas"));

         var mouse = MouseAndTouch(document, downHandler, upHandler, moveHandler);
         
         function downHandler(x,y) {
            isMouseDown = true;
            moveHandler(x,y);
         }
         
         function upHandler(x,y) {
            isMouseDown = false;
            mouseX = undefined;
            mouseY = undefined;
         }
         
         function moveHandler(x,y) {
            mouseX = (x - canvasPosition.x) / 30;
            mouseY = (y - canvasPosition.y) / 30;
         }
         
         function getBodyAtMouse() {
            mousePVec = new b2Vec2(mouseX, mouseY);
            var aabb = new b2AABB();
            aabb.lowerBound.Set(mouseX - 0.001, mouseY - 0.001);
            aabb.upperBound.Set(mouseX + 0.001, mouseY + 0.001);
            
            // Query the world for overlapping shapes.

            selectedBody = null;
            world.QueryAABB(getBodyCB, aabb);
            return selectedBody;
         }

         function getBodyCB(fixture) {
            if(fixture.GetBody().GetType() != b2Body.b2_staticBody) {
               if(fixture.GetShape().TestPoint(fixture.GetBody().GetTransform(), mousePVec)) {
                  selectedBody = fixture.GetBody();
                  return false;
               }
            }
            return true;
         }

         function getElementPosition(element) {
            var elem=element, tagname="", x=0, y=0;
           
            while((typeof(elem) == "object") && (typeof(elem.tagName) != "undefined")) {
               y += elem.offsetTop;
               x += elem.offsetLeft;
               tagname = elem.tagName.toUpperCase();

               if(tagname == "BODY")
                  elem=0;

               if(typeof(elem) == "object") {
                  if(typeof(elem.offsetParent) == "object")
                     elem = elem.offsetParent;
               }
            }

            return {x: x, y: y};
         }


         function updateMouseDrag() {
            if(isMouseDown && (!mouseJoint)) {
               var body = getBodyAtMouse();
               if(body) {
                  var md = new b2MouseJointDef();
                  md.bodyA = world.GetGroundBody();
                  md.bodyB = body;
                  md.target.Set(mouseX, mouseY);
                  md.collideConnected = true;
                  md.maxForce = 300.0 * body.GetMass();
                  mouseJoint = world.CreateJoint(md);
                  body.SetAwake(true);
               }
            }
            
            if(mouseJoint) {
               if(isMouseDown) {
                  mouseJoint.SetTarget(new b2Vec2(mouseX, mouseY));
               } else {
                  world.DestroyJoint(mouseJoint);
                  mouseJoint = null;
               }
            }
         }

(function($, others ) {
    var krwfc={};
    krwfc.methods = {
        init : function(){
            krwfc.el_id = "krwfc_cart";
            krwfc.this_cart_dragged = false;
            krwfc.ui = {
                cart : $("#krwfc_cart"),
                "krwfc_cart_header" : $(".krwfc-cart-header"),
                "krwfc_cart_header" : $(".krwfc-cart-header"),
                "krwfc_cart_header_price" : $(".krwfc_cart_header_price"),
                "krwfc_loading_img" : $("#krwfc-loading-img"),
                "krwfc_cart_inner" : $("#krwfc_cart_inner"),
                "krwfc_cart_header_total_products" : $(".krwfc_cart_header_quantity"),
                "krwfc_mini_cart" : $("#krwfc-mini-cart"),
                "krwf_cart_total_footer" : $(".krwfc-cart-total strong"),
                "krwfc_cart_empty_footer" : $(".krwfc-cart-empty-footer"),
                "krwfc_cart_footer" : $(".krwfc-cart-footer")


            };
            krwfc.data = {
                start : {left:0, top:0, right:0, bottom:0},
                diff : {x:0, y:0},

            };

            var pos = krwfc.methods.getCartPosition();

            if(pos){
                krwfc.ui.cart.css({left:pos.x, top:pos.y});
            }
            

            /* Events fired on the drag target */
            document.addEventListener("dragstart", this.ondragstart);
            
            document.addEventListener("drag", this.ondrag);
            
            /* Events fired on the drop target */
            document.addEventListener("dragover", function(event) {
                event.preventDefault();

            });
            // on drop cart
            document.addEventListener("drop", this.ondrop);

            krwfc.ui.krwfc_cart_header.click(function (e) { 
                e.preventDefault();
                krwfc.ui.krwfc_cart_inner.slideToggle(function(){},function(){
                    krwfc.methods.getcart();
                });
                
            });

            $(document.body).on('wc-blocks_added_to_cart', function(a,b,c,d){
                krwfc.methods.getcart();
            });


        },

        setCartPosition : function(x, y){

            var pos = {
                x : x,
                y : y
            }

            pos = JSON.stringify(pos);

            localStorage.setItem("krwfc_position", pos);

        },

        getCartPosition : function(){

            pos = localStorage.getItem("krwfc_position");
            if(pos){
                pos = JSON.parse(pos);
            }

            return pos;

        },


        ondragstart : function(ev){
            var id = ev.target.getAttribute("id");
            if(id == krwfc.el_id){
                krwfc.this_cart_dragged = true;
                var cartOffest = krwfc.ui.cart.position();
                krwfc.data.diff.x = ev.clientX - cartOffest.left;
                krwfc.data.diff.y = ev.clientY - cartOffest.top;

                
            }


        },
        ondrag : function(ev){},
        ondrop : function(ev){
            var id = ev.target.getAttribute("id");
            if(krwfc.this_cart_dragged){
                ev.preventDefault();
                krwfc.ui.cart.css({left:ev.clientX - krwfc.data.diff.x, top:ev.clientY - krwfc.data.diff.y});
                krwfc.this_cart_dragged = false;

                krwfc.methods.setCartPosition(ev.clientX - krwfc.data.diff.x, ev.clientY - krwfc.data.diff.y);
            }
        },
        getcart : function(){
           
                //e.preventDefault(); 
                krwfc.ui.krwfc_loading_img.show();
          
                jQuery.ajax({
                   type : "post",
                   dataType : "json",
                   url : krwfc_var.ajax_url,
                   data : {action: "krwfc_get_cart", nonce : krwfc_var.nonce},
                   success: function(response) {
                      console.log(response);
                      krwfc.methods.updateCartHtml(response);
                   }
                });
          
            
        },

        removeItemFromCart : function(product_id){
           
                //e.preventDefault(); 
                krwfc.ui.krwfc_loading_img.show();
          
                jQuery.ajax({
                   type : "post",
                   dataType : "text",
                   url : krwfc_var.ajax_url,
                   data : {action: "krwfc_remove_cart_item", nonce : krwfc_var.nonce, product_id : product_id},
                   success: function(response) {
                      console.log(response);
                      krwfc.methods.getcart();
                   }
                });
          
            
        },        
        updateCartHtml : function(data){

            // empty the cart first.
            krwfc.ui.krwfc_mini_cart.empty();

            if(data.total_amount){
                krwfc.ui.krwfc_cart_header_price.html(data.currency_symbol + data.total_amount).show();
                krwfc.ui.krwfc_cart_header_total_products.html(data.total_products).show();
                krwfc.ui.krwf_cart_total_footer.html("Total : " + data.currency_symbol + data.total_amount).show();
            }else{
                krwfc.ui.krwfc_cart_header_price.hide();
                krwfc.ui.krwfc_cart_header_total_products.hide();
                krwfc.ui.krwf_cart_total_footer.hide();
            }

            if(data.products.length){

                krwfc.ui.krwfc_cart_footer.show();
                krwfc.ui.krwfc_cart_empty_footer.hide();

                
                data.products.forEach(function(product, index){

                    var wrapper = $("<div>").addClass("krwfc-cart-item");
                    var img = $("<img>").addClass("krwfc-item-image");
                    img.attr("src",product.images);
                    wrapper.append(img);

                    var name_qty = $("<div>").addClass("name_qty");
                    var nameDiv = $("<div>").addClass("krwfc-item-name").html(product.title);
                    name_qty.append(nameDiv);

                    
                    
                    var qty = $("<span>").addClass("krwfc-item-qty").html("Qty:" + product.quantity + " ");
                    var productQtyCat = $("<div>").addClass("krwfc-item-qty-cat");
                    productQtyCat.append(qty);
                    productQtyCat.append(product.categories);

                    name_qty.append(productQtyCat);

                    wrapper.append(name_qty);

                    
                    var price = $("<div>").addClass("krwfc-item-price").html(product.price);
                    var deleteBtn = $("<span>").addClass("krwfc-item-remove").html("x");
                    

                    deleteBtn.on("click", function(){

                        krwfc.methods.removeItemFromCart(product.product_id);
                    });

                    wrapper.append(price);
                    wrapper.append(deleteBtn);

                    krwfc.ui.krwfc_mini_cart.append(wrapper);

                });
            }else{
                //krwfc.ui.krwfc_cart_inner.hide();
                krwfc.ui.krwfc_cart_footer.hide();
                krwfc.ui.krwfc_cart_empty_footer.show();
            }


            krwfc.ui.krwfc_loading_img.hide();

        }

    };




    //$("#krwfc_cart").draggableCart();
    // console.log($("#krwfc_cart").length);
    // console.log("loaded");
    $(document).ready(function(){
        krwfc.methods.init();
        krwfc.methods.getcart();
    });
    


})(jQuery);
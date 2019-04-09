
@include('frontend.elements.header')

@if(currentRoute() == '/' ) {{--currentController() from common helper--}}
    <section id="slider">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div id="slider-carousel" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">

                            @foreach(sliders() as $slider)
                                <li data-target="#slider-carousel" data-slide-to="{{ $loop->index }}" class="{{ $loop->first?'active':'' }}"></li>
                            @endforeach
                        </ol>

                        <div class="carousel-inner">
                            @foreach(sliders() as $slider)
                                <div class="item {{ $loop->first?'active':'' }}">
                                    <img src="{{ URL::to('public/admin/uploads/images/sliders/'.$slider->image) }}"
                                         class="girl img-responsive" alt=""/>
                                </div>
                            @endforeach
                        </div>

                        {{--<a href="#slider-carousel" class="left control-carousel hidden-xs" data-slide="prev">
                            <i class="fa fa-angle-left"></i>
                        </a>
                        <a href="#slider-carousel" class="right control-carousel hidden-xs" data-slide="next">
                            <i class="fa fa-angle-right"></i>
                        </a>--}}
                    </div>

                </div>
            </div>
        </div>
    </section>
@endif

<section>
    <div class="container">
        <div class="row">

            @if(currentController() == 'HomeController')

                <!--search section-->
                <div class="header-bottom">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-8">

                            </div>
                            <div class="col-sm-4">
                                <div class="search_box pull-right">

                                    <form action="{{ route('products.search') }}" method="get" class="search_box">
                                        <input name="search" type="text" value="{{ !empty(Request::get('search'))?Request::get('search'):'' }}" placeholder="Search product"/>                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(currentController() == 'HomeController' OR currentController() == 'ProductsController')
                @include('frontend.elements.sidebar')

                <div class="col-sm-9 padding-right">
                    @yield('content')
                </div>
            @else
                <div class="col-sm-12 padding-right">
                    @yield('content')
                </div>
            @endif

        </div>
    </div>

</section>

@include('frontend.elements.footer')

<script src="{{ asset('public/frontend/js/vue/vue.js') }}"></script>
<script src="{{ asset('public/frontend/js/axios/axios.js') }}"></script>

<script type="text/javascript">

    //zoom....
    $(document).ready(function(){
        $('.zoomple').zoomple({
            offset : {x:-150,y:-150},
            zoomWidth : 300,
            zoomHeight : 300,
            roundedCorners : true
        });


        $("#rateBox").rate({
            length: 5,
            value: 0,
            readonly: false,
            size: '18px',
            selectClass: 'fxss_rate_select',
            incompleteClass: 'fxss_rate_no_all_select',
            customClass: 'custom_class',
            callback: function(object){
                $('#rating').val(object.index+1);
            }
        });

    });

    var App = new Vue({
        el: "#root",
        data: {
            wishlists:[],
            carts:[],
            product:{
                qty: 1,
            }
        },

        mounted(){
            this.getAllWishlistProduct();
            this.getAllCartProduct();
            this.updateWishlistCounter();
            this.updateCartCounter()
            this.updateCartFinalCalculation();
        },

        methods:{

            //Start Wishlist...............

                //Get all wishlist product
                getAllWishlistProduct(){
                    currentApp = this;
                    axios.get(home_url + '/wishlists/get/product')
                        .then(response => {
                            currentApp.wishlists = response.data;
                        })
                },

                //Product add to wishlist
                addToWishlist(e) {

                    slug = e.currentTarget.getAttribute('slug');
                    currentApp = this

                    axios.get(home_url + '/wishlists/'+slug)
                        .then(response => {

                            if (response) {

                                //sweet alert
                                swal(response.data.title, response.data.message, response.data.type);
                            }

                            if (response.data.type == 'success'){
                                currentApp.updateWishlistCounter();
                            }
                        });
                },

                //Product remove from wishlist
                removeFromWishlist(e){

                    currentApp = this
                    row_id = e.currentTarget.getAttribute('row-id');

                    //sweet alert
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this item!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    }, function () {

                        axios.get(home_url + '/wishlists/remove/'+row_id)
                            .then(response => {

                            if (response){
                                swal(response.data.title, response.data.message, response.data.type);
                            }

                            if (response.data.type == 'success'){
                                currentApp.getAllWishlistProduct();
                                currentApp.updateWishlistCounter();
                            }
                        })
                    });
                },

                //Product move to cart from wishlist
                moveToCart(e){

                    row_id = e.currentTarget.getAttribute('row-id')
                    axios.get(home_url + '/wishlists/move-cart/'+row_id)   //response = total count wishlist product
                        .then(response => {

                            if (response) {

                                //sweet alert
                                swal(response.data.title, response.data.message, response.data.type);
                            }

                            if (response.data.type == 'success'){
                                currentApp.getAllWishlistProduct();
                                currentApp.updateWishlistCounter();
                                currentApp.updateCartCounter();
                            }

                        });

                },

                //Instance update wishlist product counter
                updateWishlistCounter(){
                    axios.get(home_url + '/wishlists/count/product')   //response = total count wishlist product
                        .then(response => {

                            $('#wishlist-counter').addClass('counter');
                            $('#wishlist-counter').html(response.data);
                        });
                },

            //End Wishlist...............



            //Start Product...............

                //Get all cart product
                getAllCartProduct(){
                    currentApp = this;
                    axios.get(home_url + '/carts/get/product')
                        .then(response => {
                            currentApp.carts = response.data;
                        })
                },

                //Product add to cart
                addToCart(e) {

                    currentApp = this;
                    slug = e.currentTarget.getAttribute('slug');

                    if (this.product.qty <= 0){
                        swal('Warning', 'Quantity must be a valid number.', 'warning');
                        return;
                    }

                    axios.post(home_url+'/carts/'+slug, this.product)
                        .then(response => {

                            if (response) {

                                //sweet alert
                                swal(response.data.title, response.data.message, response.data.type);
                                currentApp.updateCartCounter();
                            }

                        });
                },

                //Product remove from wishlist
                removeFromCart(e){

                    currentApp = this
                    row_id = e.currentTarget.getAttribute('row-id');

                    //sweet alert
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this item!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    }, function () {

                        axios.get(home_url + '/carts/remove/'+row_id)
                            .then(response => {

                                if (response){
                                    swal(response.data.title, response.data.message, response.data.type);
                                }

                                if (response.data.type == 'success'){
                                    currentApp.getAllCartProduct();
                                    currentApp.updateCartCounter();
                                    currentApp.updateCartFinalCalculation();
                                }
                            })
                    });
                },

                //Instance update cart product counter
                updateCartCounter(){
                    axios.get(home_url + '/carts/count/product')   //response = total count cart product
                        .then(response => {
                            $('#cart-counter').addClass('counter');
                            $('#cart-counter').html(response.data);
                        });
                },

                //Instance update cart product total price calculation
                updateCartFinalCalculation(){

                    axios.get(home_url + '/carts/final-calculate')
                        .then(response => {
                            $('#cart-sub-total').html(response.data.subTotal)
                            $('#cart-total').html(response.data.total)
                        });
                },

            //Start Product...............
        }
    })
</script>
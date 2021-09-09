@extends($_viewFrame)

@section('pageTitle'){{(!empty($pageTitle)?$pageTitle.' - ':'').modstart_config('siteName')}}@endsection

@section('bodyContent')

    <div class="ub-container margin-top">
        <div class="row">
            <div class="col-md-2">
                <div class="ub-menu nav-menu tw-mb-2">
                    @foreach(\Module\Member\Config\MemberMenu::get() as $menu)
                        <div class="title">{!! $menu['icon'] !!} {{$menu['title']}}</div>
                        <div class="items">
                            @foreach($menu['children'] as $item)
                                <a class="{{modstart_baseurl_active($item['url'])}}" href="{{$item['url']}}">{{$item['title']}}</a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <script>
                    $(function () {
                        var $menu = $('.ub-menu.nav-menu');
                        if($(window).width()>600){
                            $menu.find('.items').addClass('open');
                        }else{
                            $menu.find('.title').on('click',function () {
                                $menu.find('.items').removeClass('open');
                                $(this).next().addClass('open')
                                    .css('left',$(this).offset().left+'px')
                                    .css('width',$(this).width()+'px');
                                return false;
                            });
                            $(document).on('click',function(){
                                $menu.find('.items').removeClass('open');
                            });
                        }
                    });
                </script>
            </div>
            <div class="col-md-10">
                @section('memberBodyContent')
                    {!! $content or '' !!}
                @show
            </div>
        </div>
    </div>

@endsection

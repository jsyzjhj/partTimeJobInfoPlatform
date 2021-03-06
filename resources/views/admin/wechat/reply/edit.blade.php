
@extends('layouts.app')

@section('css')
    <style type="text/css">
        .alert{
            background-color: #eee;
        }
        .box-tools{
            display: block;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid" style="padding: 30px 15px;">
        <div class="row">
            <div class="col-sm-3 col-lg-2">
                <ul class="nav nav-pills nav-stacked nav-email">
                    <li class="{{ Request::is('zcjy/wechat/menu/menu') ? 'active' : '' }}">
                        <a href="{!! route('wechat.menu') !!}">
                            <span class="badge pull-right"></span>
                            <i class="fa fa-user"></i> 菜单设置
                        </a>
                    </li>
                    <li class="{{ Request::is('zcjy/wechat/reply') ? 'active' : '' }}">
                        <a href="{!! route('wechat.reply') !!}">
                            <span class="badge pull-right"></span>
                            <i class="fa fa-users"></i> 回复消息
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-sm-9 col-lg-10">
                <div class="content pdall0-xs">
                    <div class="box box-primary form">
                        <div class="box-body">
                            <div class="container">
                                
                                <div style="margin-top: 10px;">
                                    <button type="button" class="btn btn-flat btn-primary">关键词自动回复</button>
                                    <a type="button" class="btn btn-default" href="/zcjy/wechat/reply/rpl-follow">被关注时回复</a>
                                    <a type="button" class="btn btn-default" href="/zcjy/wechat/reply/rpl-no-match">无匹配时回复</a>
                                </div>
                            
                                <!-- 右侧菜单设置 -->
                                <div class="col-lg-12 col-md-12" >
                                    <div class="nav-tabs-custom row" id="nav-tabs-custom" style="margin-top: 20px;">
                                        <div style="padding: 15px;">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">规则名<small class="lite-gray">规则名最多60个字</small></label>
                                                <input type="email" v-model='ruleNmae' class="form-control" id="exampleInputEmail1" placeholder="请输入规则名" maxlength="60">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">关键子 <small class="lite-gray">多个关键字用英文逗号分隔，最多输入30字</small></label>
                                                <input type="email" v-model='ruleKeyWord' class="form-control" id="exampleInputEmail1" placeholder="请输入触发关键字" maxlength="30">
                                            </div>
                                            <div class="form-group">
                                                <label>触发方式: </label>
                                                <label style="margin-right: 15px; margin-left: 10px;">
                                                    <input type="radio" v-model='ruleTriggerType' class="minimal" value="equal" /> 等于
                                                </label>
                                                <label>
                                                    <input type="radio" v-model='ruleTriggerType' class="minimal" value="contain" /> 包含
                                                </label>
                                            </div>
                                        </div>
                                        <div class="box" style="padding: 15px">
                                            <label style="margin-bottom: 15px;">回复内容: </label>
                                            <replies v-for="(item, index) in replies" :item=item :pos="index"></replies>
                                        </div>
                                        <ul class="nav nav-tabs">
                                            <!-- 顶部切换按钮 -->
                                            <li><a href="#textmodal" data-toggle="modal" aria-expanded="true"><i class="fa fa-comments"></i> <span>文字</span></a></li>
                                            <!--li @click=changeType('article')><a href="#material-selector" data-toggle="modal" aria-expanded="true"><i class="fa fa-photo"></i> <span>图文消息</span></a></li>
                                            
                                            <li @click=changeType('image')><a href="#material-selector" data-toggle="modal" aria-expanded="true"><i class="fa fa-camera-retro"></i> <span>图片</span></a></li>
                                            <li @click=changeType('voice')><a href="#material-selector" data-toggle="modal" aria-expanded="true"><i class="fa fa-volume-down"></i> <span>语音</span></a></li>
                                            <li @click=changeType('video')><a href="#material-selector" data-toggle="modal" aria-expanded="true"><i class="fa fa-caret-square-o-right"></i> <span>视频</span></a></li-->
                                        </ul>
                                        
                                        <div class="tc col-md-12">
                                            <div class="row" style="padding-bottom: 20px; ">
                                                <div class="btn btn-primary" @click="save">保存</div>
                                                <a class="btn" href="/zcjy/wechat/reply" @click="cancel">取消</a>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- 设置自动回复文字 -->
                            <div class="modal fade " id="textmodal">
                                <div class="modal-dialog" >
                                    <div class="modal-content center" style="width: 500px;">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">设置自动回复文字</h4>
                                        </div>
                                        <div class="modal-body" style="height: 200px;">
                                            <div id="texteditor" rowspan='5'></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" id="save_text_selection">保存</button>
                                        </div>
                                        </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Popup itself -->
                        <div class="modal fade" id="material-selector">
                            <div class="modal-dialog">
                                <div class="modal-content center media-popu-width">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h4 class="modal-title">选择素材</h4>
                                    </div>
                                    <div class="modal-body modal-body-matiral">
                                        <div class="infinitescroll" style="overflow: hidden;">
                                            <!--div class="material-item-article material-item">
                                            <div class="img"><img src="http://dummyimage.com/800x600/4d494d/686a82.gif&text=placeholder+image"></div>
                                            <p>标题</p>
                                        </div-->
                                    </div>
                                </div>
                                <div class="addmore btn" >点击此处加载更多素材</div>
                                <div id="navigation"><a href="/zcjy/wechat/material/lists?page=1&type=image"></a> </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="save_material_selection">保存</button>
                                </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('scripts')
    <template id="replies">
        <div class="alert">
            <button type="button" class="close" @click='cancelReply'>×</button>
            @{{text}}
        </div>
    </template>

    <script src="{{ asset('vendor/vue.js') }}"></script>
    <script src="{{ asset('vendor/vuex.min.js') }}"></script>
    <script src="{{ asset('vendor/wechat-editor.js') }}"></script>
    <script src="{{ asset('vendor/underscore-min.js') }}"></script>
    <script type="text/javascript">

  
        $(document).ready(function(){

            //数据状态保存
            const store = new Vuex.Store({
                state: {
                    page: 1,    //分页加载media的页数
                    type: 'text',   //分页加载的类型
                    display: false,  //是否显示消息编辑内容
                    mediaselection: {img_url:null, name: null, type: null, media_id: null}, //用户选择的media
                    replies: [],
                },
                mutations: {
                    pageInfo (state, payload) {
                        state.page = payload.page;
                        state.type = payload.type;
                    },
                    selectionInfo (state, payload) {
                        state.mediaselection.img_url = payload.img_url;
                        state.mediaselection.name = payload.name;
                        state.mediaselection.type = payload.type;
                        state.mediaselection.media_id = payload.media_id;
                        //console.log(payload);
                    },
                    refreshMediaSelection(state, payload){
                        state.mediaselection.img_url = null;
                        state.mediaselection.name = null;
                        state.mediaselection.type = null;
                        state.mediaselection.media_id = null;
                    },
                    addReply(state, payload){
                        if ( payload.name == null || (payload.type=='text' && payload.name == '') ) {
                            return ;
                        }
                        //用户可以多选，这要保存用户的选择
                        if (state.replies.length > 4) {
                            //最多只能加5个
                            layer.msg("最多只能加5个回复!", {icon: 5});
                        } else {
                            state.replies.push({name: payload.name, type: payload.type, media_id: payload.media_id, text: payload.text });
                            ////console.log(state.replies);
                        }
                    },
                    cancelReply(state, payload){
                        state.replies.splice(payload.pos,1);
                    },
                    switchDisplay(state, status) {
                        state.display = status;
                        //console.log(state.display);
                    }
                }
            })

            //单个回复内容设置
            Vue.component('replies', {
                template: "#replies",
                props: ['item', 'pos'],
                computed:{
                    text: function () {
                        switch(this.item.type){
                            case 'article':
                                return '类型：图文    名称：' + this.item.name;
                            break;
                            case 'text':
                                return '类型：文字';
                            break;
                            case 'image':
                                return '类型：图片    名称：' + this.item.name;
                            break;
                            case 'voice':
                                return '类型：声音    名称：' + this.item.name;
                            break;
                            case 'video':
                                return '类型：视频    名称：' + this.item.name;
                            break;
                        }
                        
                    }
                },
                methods: {
                    cancelReply: function () {
                        store.commit('cancelReply', {pos: this.pos});
                    }
                }
            })

            //文本编辑器
            var wechatEditor = new WeChatEditor($('#texteditor'), {textarea: 'text'});

            //加载更多资源进行选择
            $('div.addmore').on('click', function () {
                LoadMarerial();
            })

            //保存资源选择
            $('#save_material_selection').on('click', function(){
                store.commit('addReply', {name: store.state.mediaselection.name, type: store.state.mediaselection.type, media_id: store.state.mediaselection.media_id, text: $('textarea[name=text]').text() });
                //关闭弹窗
                $('#material-selector .close').click();
            })

            $('#save_text_selection').on('click', function(){
                store.commit('selectionInfo', {
                    img_url:null, 
                    name: '文本回复', 
                    type: 'text', 
                    media_id: null
                });
                store.commit('addReply', {name: store.state.mediaselection.name, type: store.state.mediaselection.type, media_id: store.state.mediaselection.media_id, text: $('textarea[name=text]').text() });
                //关闭弹窗
                $('#textmodal .close').click();
            })

            //菜单选择标签页
            var mediaVue = new Vue({
                el: '#nav-tabs-custom',
                store,
                data: {
                    media: null, //media数据
                    display_type: 'text', //tab显示类型
                    //canseen: store.state.display,
                    view_url: '',
                    text: '',
                    ruleNmae: 'ruleNmae',
                    ruleKeyWord: 'ruleKeyWord',
                    ruleTriggerType:'equal',
                },
                computed: {
                    replies: function () {
                        return store.state.replies;
                    },
                    canseen: function () {
                        return store.state.display;
                    },
                    isText: function () {
                        return this.display_type == 'text';
                    },
                    isArticle: function () {
                        return this.display_type == 'article';
                    },
                    isImage: function () {
                        return this.display_type == 'image';
                    },
                    isVoice: function () {
                        return this.display_type == 'voice';
                    },
                    isVideo: function () {
                        return this.display_type == 'video';
                    },
                },
                mounted (){
                    _self = this;
                    $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    $.ajax({
                        url:"/zcjy/wechat/reply/single/{{$id}}",   //获取菜单信息
                        type:'GET', //GET
                        async:true,    //或false,是否异步
                        timeout:5000,    //超时时间
                        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
                        success:function(data,textStatus,jqXHR){
                            _self.ruleNmae = data.name;
                            _self.ruleKeyWord = data.trigger_keywords;
                            _self.ruleTriggerType = data.trigger_type;
                            //回复内容
                            //console.log(data.content);
                            for (var i = 0; i < data.content.length; i++) {
                                if (data.content[i].type == 'text') {
                                    store.commit('addReply', {name: '文本回复', type: data.content[i].type, media_id: data.content[i].media_id, text: data.content[i].content });
                                } else {
                                    store.commit('addReply', {name: data.content[i].title, type: data.content[i].type, media_id: data.content[i].media_id, text: data.content[i].content });
                                }
                            }
                        }
                    });
                },
                methods: {
                    changeType: function (type) {
                        //设置资源请求信息
                        store.commit('pageInfo', {type: type, page: 1});
                        store.commit('refreshMediaSelection');
                        //清空已经加载的内容
                        $('.infinitescroll').empty();
                        LoadMarerial();
                        //console.log('clicked:'+type);
                    },
                    save: function () {
                        var replaydata = {
                            name: this.ruleNmae,
                            trigger_keywords: this.ruleKeyWord,
                            trigger_type: this.ruleTriggerType,
                            replies: store.state.replies
                        };
                        _self = this;
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url:'/zcjy/wechat/reply/update/{{$id}}',   //获取菜单信息
                            data: replaydata,
                            type:'POST', //GET
                            async:true,    //或false,是否异步
                            timeout:5000,    //超时时间
                            dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
                            beforeSend:function(xhr){
                                //console.log(xhr)
                                //console.log('发送前')
                            },
                            success:function(data,textStatus,jqXHR){

                                layer.msg("保存成功!", {icon: 1});
                            },
                            error:function(xhr,textStatus){
                                //console.log('错误')
                                //console.log(xhr)
                                //console.log(textStatus)
                            },
                            complete:function(){
                                //console.log('结束')
                            }
                        });

                    },
                    cancel: function () {
                        store.commit('switchDisplay', false);
                    }
                }
            })

            //加载资源
            function LoadMarerial() {
                var requesturl = '/zcjy/wechat/material/lists?page='+ store.state.page + '&type='+ store.state.type;
                $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                $.ajax({
                    url: requesturl,
                    type:'GET', //GET
                    async:true,    //或false,是否异步
                    timeout:5000,    //超时时间
                    dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
                    beforeSend:function(xhr){
                        //console.log(xhr)
                        //console.log('发送前')
                    },
                    success:function(data,textStatus,jqXHR){
                        //console.log(data['data']);
                        if(data['current_page'] == data['last_page']){
                            //最后一页
                            $('.addmore').text('没有更多的素材可供加载').delay(2000).hide(2000);
                        }
                        //store.state.page = ++store.state.page;
                        store.commit('pageInfo', {type: store.state.type, page: ++store.state.page})
                        //组装HTML
                        var items = data['data'];
                        var items_length = items.length;
                        for (var i = 0; i < items_length; i++) {
                            switch (items[i].type){
                                case 'article':
                                    $('.infinitescroll').append(
                                    "<div class='material-item-article material-item' media_id='" + items[i].media_id + "' media_tpye='" + items[i].type + "'>\
                                        <div class='img'><img src='"+ items[i].cover_url +"'></div> \
                                        <p>"+ items[i].title +"</p>\
                                    </div>"
                                    );
                                break;
                                case 'image':
                                    $('.infinitescroll').append(
                                    "<div class='material-item-article material-item' media_id='" + items[i].media_id + "' media_tpye='" + items[i].type + "'>\
                                        <div class='img'><img src='"+ items[i].source_url +"'></div> \
                                        <p>"+ items[i].title +"</p>\
                                    </div>"
                                    );
                                break;
                                case 'voice':
                                    $('.infinitescroll').append(
                                    "<div class='material-item-article material-item' media_id='" + items[i].media_id + "' media_tpye='" + items[i].type + "'>\
                                        <div class='img-voice'><p>"+ items[i].title +"</p></div></div>"
                                    );
                                break;
                                case 'video':
                                    $('.infinitescroll').append(
                                    "<div class='material-item-article material-item' media_id='" + items[i].media_id + "' media_tpye='" + items[i].type + "'>\
                                        <div class='img-video'><p>"+ items[i].title +"</p></div></div>"
                                    );
                                break;
                            }
                        }
                        $('.infinitescroll .material-item-article').unbind('click');
                        $('.infinitescroll .material-item-article').on('click', function(){
                            $('.infinitescroll .material-item-article').removeClass('imclicked');
                            $(this).addClass('imclicked');
                            store.commit('selectionInfo', {
                                img_url:$(this).find('img').attr('src'), 
                                name: $(this).find('p').text(), 
                                type: $(this).attr('media_tpye'), 
                                media_id: $(this).attr('media_id')
                            });
                            //console.log('你选择的是：'+ store.state.mediaselection.media_id);
                        })

                    },
                    error:function(xhr,textStatus){
                        //console.log('错误')
                        //console.log(xhr)
                        //console.log(textStatus)
                    },
                    complete:function(){
                        //console.log('结束')
                    }
                })
            }
        });
    </script>
@endsection
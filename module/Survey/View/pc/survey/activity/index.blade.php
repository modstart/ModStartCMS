@extends($_viewFrame)

@section('pageTitleMain'){{$activity['name']}}@endsection

@section('bodyContent')

    <div class="ub-container tw-bg-white" style="max-width:800px;">

        @if(!empty($activity['cover']))
            <div class="">
                <img class="tw-w-full" src="{{\ModStart\Core\Assets\AssetsUtil::fix($activity['cover'])}}"/>
            </div>
        @endif

        @if(!empty($activity['description']))
            <div class="tw-p-4">
                <div class="ub-html">
                    {!! $activity['description'] !!}
                </div>
            </div>
        @endif

        <div class="lg:tw-p-4 tw-p-0">
            <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" method="post" data-ajax-form>
                <div class="ub-form vertical">
                    @foreach($questions as $question)
                        <div class="line">
                            <div class="label tw-relative">
                                @if($question['required'])
                                    <div style="position:absolute;left:-8px;top:0px;color:red;">*</div>
                                @endif
                                <div class="ub-html">
                                    {!! \ModStart\Core\Util\HtmlUtil::text2html($question['body']) !!}
                                </div>
                            </div>
                            <div class="field">
                                @if($question['type']==\Module\Survey\Type\SurveyQuestionType::SINGLE_CHOICE)
                                    <div>
                                        @foreach($question['choice'] as $choiceIndex=>$choice)
                                            <label>
                                                <input type="radio" name="field-{{$question['id']}}" value="{{$choice}}"/>
                                                {{$choice}}
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @if($question['type']==\Module\Survey\Type\SurveyQuestionType::MULTI_CHOICE)
                                    <div>
                                        @foreach($question['choice'] as $choiceIndex=>$choice)
                                            <label>
                                                <input type="checkbox" name="field-{{$question['id']}}[]" value="{{$choice}}"/>
                                                {{$choice}}
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @if($question['type']==\Module\Survey\Type\SurveyQuestionType::TEXT)
                                    <div class="question-answer">
                                        <input type="text" class="form sm" name="field-{{$question['id']}}" value=""/>
                                    </div>
                                @endif
                                @if($question['type']==\Module\Survey\Type\SurveyQuestionType::BIG_TEXT)
                                    <div class="question-answer">
                                        <textarea class="form" style="min-height:5rem;" name="field-{{$question['id']}}" rows="3"></textarea>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="line" style="margin-top:1rem;">
                        <div class="field">
                            <button class="btn btn-primary btn-block btn-lg" type="submit">提交</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

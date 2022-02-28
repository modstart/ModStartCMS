<div style="width:400px;background:#FFF;padding:5px;border-radius:5px;">
    <table class="ub-table mini">
        <tbody>
        @foreach($answerItems as $answerItem)
            <tr style="background:#FFF;">
                <td width="80">
                    @if($answerItem['_question']['type']==\Module\Survey\Type\SurveyQuestionType::SINGLE_CHOICE)
                        <span class="ub-color-a">单选题</span>
                    @endif
                    @if($answerItem['_question']['type']==\Module\Survey\Type\SurveyQuestionType::MULTI_CHOICE)
                        <span class="ub-color-b">多选题</span>
                    @endif
                    @if($answerItem['_question']['type']==\Module\Survey\Type\SurveyQuestionType::TEXT)
                        <span class="ub-color-c">单行文本</span>
                    @endif
                    @if($answerItem['_question']['type']==\Module\Survey\Type\SurveyQuestionType::BIG_TEXT)
                        <span class="ub-color-d">多行文本</span>
                    @endif
                </td>
                <td>
                    <div class="ub-html">
                        {!! \ModStart\Core\Util\HtmlUtil::text2html($answerItem['_question']['body']) !!}
                    </div>
                </td>
                <td>
                    <div class="ub-html">
                        @if($answerItem['_question']['type']==\Module\Survey\Type\SurveyQuestionType::SINGLE_CHOICE)
                            {!! \ModStart\Core\Util\HtmlUtil::text2html($answerItem['body']) !!}
                        @endif
                        @if($answerItem['_question']['type']==\Module\Survey\Type\SurveyQuestionType::MULTI_CHOICE)
                            {!! \ModStart\Core\Util\HtmlUtil::text2html(join(',',json_decode($answerItem['body'],true))) !!}
                        @endif
                        @if($answerItem['_question']['type']==\Module\Survey\Type\SurveyQuestionType::TEXT)
                            {!! \ModStart\Core\Util\HtmlUtil::text2html($answerItem['body']) !!}
                        @endif
                        @if($answerItem['_question']['type']==\Module\Survey\Type\SurveyQuestionType::BIG_TEXT)
                            {!! \ModStart\Core\Util\HtmlUtil::text2html($answerItem['body']) !!}
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>

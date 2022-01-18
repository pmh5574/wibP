<form name="frmWrite" id="frmWrite" action="/board/best_review_go.php" method="post" enctype="multipart/form-data" target="ifrmProcess" class="content-form js-setup-form">
<input type="hidden" name="sno" value="<?= $sno ?>">
<input type="hidden" name="bdId" value="store">
<input type="hidden" name="mode" value="ajaxUpload">
<table class="table table-cols" id="board-table">
    <tr>
        <th>파일첨부</th>
        <td>
            <ul class="pdl0" id="uploadBox">
               
                <li class="form-inline mgb5">
                    <input type="file" name="upfiles[]">
                    <a class="btn btn-white btn-icon-plus addUploadBtn btn-sm">추가</a>
                </li>
                
            </ul>
            <div class="notice-info">
                파일은 최대 2개까지 다중업로드가 지원됩니다.         
            </div>
        </td>
    </tr>
</table>
<div>  
    <input type="submit" value="저장하기" class="btn btn-red"/>
</div>
</form>
<script type="text/javascript">
    $('body').on('click', '.addUploadBtn', function () {
        var uploadBoxCount = $('#uploadBox').find('input[name="upfiles[]"]').length;
        if (uploadBoxCount >= 2) {
            alert("업로드는 최대 2개만 지원합니다");
            return;
        }

        var addUploadBox = _.template(
            $("script.template").html()
        );
        $(this).closest('ul').append(addUploadBox);
        init_file_style();
    });

    $('body').on('click', '.minusUploadBtn', function () {
        index = $(this).prevAll('input:file').attr('index'); //$('.file-upload button.uploadremove').index(target)+1;
        $("input[name='uploadFileNm[" + index + "]']").remove();
        $("input[name='saveFileNm[" + index + "]']").remove();
        $(this).closest('li').remove();
    });

    $(document).on("change", "input:file", function () {
        //ajax업로드 처리
        console.log(this);
        var thisObj = $(this);
        var uploadImages = [];
        console.log(this.name);
        var name = this.name;
        var idx = $('input[name="' + name + '"]').index(this);
        console.log(idx);
        gdAjaxUpload.upload(
            {
                formObj: $("#frmWrite"),
                thisObj: thisObj,
                actionUrl: './article_ps.php',
                params: {bdId: $('[name=bdId]').val(), 'mode': 'ajaxUpload'},
                onbeforeunload: function () {
                    if (uploadImages.length == 0) {
                        return false;
                    }
                    $.ajax({
                        method: "POST",
                        url: "./article_ps.php",
                        async: false,
                        data: {mode: 'deleteGarbageImage', bdId: $('[name=bdId]').val(), deleteImage: uploadImages.join('^|^')},
                        cache: false,
                    }).success(function (data) {
                    }).error(function (e) {
                    });
                },
                successAfter: function (data) {
                    console.log(data.index);
                    thisObj.attr('index',data.index);
                    console.log(data.saveFileNm);
                    uploadImages.push(data.saveFileNm);
                },
                failAfter : function(data) {
                    if (data.result == 'fail' && name == 'upfiles[]') {
                        $('input[name="' + name + '"]').eq(idx).val('');
                    }
                }
            }
        )
    });
</script>

<script type="text/template" class="template">
    <li class="form-inline mgb5">
        <input type="file" name="upfiles[]">
        <a class="btn btn-white btn-icon-minus minusUploadBtn btn-sm">삭제</a>
    </li>
</script>
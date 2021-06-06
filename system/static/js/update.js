function Upload() {
    const LENGTH = 1024 * 512;
    let start = 0;
    let end = start + LENGTH;
    let blob;
    let blob_num = -1;

    this.uploadFile=function (that) {
        let file = that.files[0];
        while (start < file.size){
            blob = cutFile(file);
            sendFile(blob,file);

        }
    }


    function cutFile(file){
        let file_blob = file.slice(start,end);
        start = end;
        end = start + LENGTH;
        return file_blob;
    }
    function sendFile(thatBlob,file){
        $.post("./?/uploadUpdate",{
            file:blob,
            file_name:file.name+blob_num
        },function (data){
            console.log(data);
        });
    }
}
$(document).ready(function(){
  var jid = "";
  $("button").click(function(){
    var url = $("[name='url']").val();
    var cat = $("[name='cat']").val();
    var subcat = $("[name='subcat']").val();
    var movie = $("[name='movie']").val();
    var file = $("[name='file']").val();
    $.ajax({
      type:"POST",
      url:baseUrl+"/encoder/job",
      data:{url:url,cat:cat,subcat:subcat,movie:movie,file:file},
      beforeSend:function(){
        $("[name='loader']").show();
      },
      success:function(data){
        if (parseInt(data) > 0){
          jid = data;
          $("#jobId").text(" | " + jid);
        }else{
          $(".err").html("something wrong with your input url.");
          $(".err").show();
        }
      },
      error:function(err){
        $(".err").html("something wrong with your input url.");
        $(".err").show();
      }
    });

  var reader = setInterval(function(){
  if (parseInt(jid) > 0){
    $.ajax({
      url:baseUrl+"/encoder/read?id=" + jid,
      success:function(data){
        var status = "";
        var output = "";
        var d = JSON.parse(data);
        if (undefined === d.transfer){
          status = d.job.status;
        }else{
          status = d.transfer[0].status;
          output = d.transfer[0].outputProfile.outputUrl;
        }
        if (status == "finished" || status == "error"){
          clearInterval(reader);
          $("[name='loader']").hide();
          $(".err").html("encoding completed and output " + output);
          $(".err").show();
        }
        if (status == "error"){
          $(".err").html("something wrong with bitmovin. jobid = " + jid);
          $(".err").show();
        }
        console.log(d);
      },
      error:function(){

      }
    });
  }
},5000);
  });
});

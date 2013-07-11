 $(document).ready(function(){
        $("#save-privacy-button").click(function(){
            $element_success = $("#EditProfile-privacy-form").find(".flash-success");
            $element_error = $("#EditProfile-privacy-form").find(".flash-error");
            
            $listRadio = $("#EditProfile-privacy-form").find("input[type='radio']:checked");
            
            var arrayData = new Object();
            for(i=0; i<($listRadio.length - 1); i++){
                  arrayData[$listRadio.eq(i).attr("name")] = $listRadio.eq(i).val();
            }
            
            $.ajax({
                type: "POST",
                url: "http://localhost/TrainingLog/index.php/user/saveprivacy",
                data: {privacy: arrayData, user: "1"},
                success: function(data, status){
                    try{
                        xmlDocumentElement = data.documentElement; //korenovy prvok xml dokumentu
                        bool_value = xmlDocumentElement.firstChild.data;

//                        if(bool_value === "true"){
//                            if ( $element_error.is(':visible') ){ //Ak je viditelny neuspesny flash tak ho shovame
//                                $element_error.hide();
//                            }
//                            $element_success.slideDown("slow");
//                        }
//                        else{
//                            $element_error.slideDown("slow");
//                        }
                          $element_success.slideDown("slow");
                          $element_success.html(document.createTextNode(bool_value));
                    }
                    catch(e){
                        if ( !$element_error.is(':visible') ){//Textovy uzol nedoplname viac ako raz
                            $element_error.append(document.createTextNode(". Error reading response"));
                        }
                        $element_error.slideDown("slow");
                    }
                },
                error: function(jqXHR,textStatus,errorThrown){ 
                    if ( !$element_error.is(':visible') ){//Textovy uzol nedoplname viac ako raz
                        $element_error.append(document.createTextNode(". Request problems "+errorThrown));
                    }
                    $element_error.slideDown("slow");
                }
            });
        });
    });
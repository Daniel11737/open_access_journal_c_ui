document.addEventListener('DOMContentLoaded', function () {
  var tabButtons = document.querySelectorAll('.nav-link');
  var tabContent = document.querySelectorAll('.tab-pane input');
  var selectedTabIndex = 0;
  var prevBtn = document.getElementById("prev");
  var nextBtn = document.getElementById("next");
  var checkDuplicateBtn = document.getElementById("check-d");
  var submitBtn = document.getElementById("submit");
  var form = document.getElementById("form");
  var inputFields = form.querySelectorAll("input");

  for (var i = 1; i < tabButtons.length; i++) {
    tabButtons[i].disabled = true;
  }


  function updateButtonVisibility(index) {
    // Display buttons based on the current tab index
    if (index === 0) {
      prevBtn.style.display = "none";
      nextBtn.style.display = "inline-block";
      submitBtn.style.display = "none";
      checkDuplicateBtn.style.display = "none";
    }else if(index === 1){
      prevBtn.style.display = "inline-block";
      nextBtn.style.display = "inline-block";
      submitBtn.style.display = "none";
      checkDuplicateBtn.style.display = "inline-block";
    } 
    else if (index > 0 && index < tabButtons.length - 1) {
      prevBtn.style.display = "inline-block";
      nextBtn.style.display = "inline-block";
      submitBtn.style.display = "none";
      checkDuplicateBtn.style.display = "none";
    } else if (index === tabButtons.length - 1) {
      prevBtn.style.display = "inline-block";
      nextBtn.style.display = "none";
      submitBtn.style.display = "inline-block";
      checkDuplicateBtn.style.display = "none";
    }
  }

  function updateStyles() {
    tabButtons.forEach(function (btn, i) {
      if (i === selectedTabIndex) {
        btn.style.backgroundColor = "#115272";
        btn.style.color = "white";
      } else {
        btn.style.backgroundColor = "white";
        btn.style.border = "1px solid";
        btn.style.color = "#115272";
      }
    });
  }

  function updateButtonStates(index) {
    for (var i = index + 1; i < tabButtons.length; i++) {
      tabButtons[i].disabled = tabContent[i - 1].value === '';
    }
  }

  function switchToNextTab() {
    if (selectedTabIndex < tabButtons.length - 1) {
      inputFields = tabContent[selectedTabIndex];
      if (inputFields.value === '' || tabButtons[selectedTabIndex + 1].disabled) {
        return; // Do nothing if the input field is empty or the next button is disabled
      }

      tabButtons[selectedTabIndex + 1].click(); // Simulate a click on the next tab button
    }
  }

  function switchToPrevTab() {
    if (selectedTabIndex > 0) {
      tabButtons[selectedTabIndex - 1].click(); // Simulate a click on the previous tab button
    }
  }

  tabContent.forEach(function (input, index) {
    input.addEventListener('input', function () {
      updateButtonStates(index);
      updateStyles();
    });

    input.addEventListener('focus', function () {
      updateStyles();
    });

    input.addEventListener('blur', function () {
      // Don't change styles on blur
    });
  });

  tabButtons.forEach(function (button, index) {
    button.addEventListener('click', function () {
      selectedTabIndex = index;
      updateButtonVisibility(index);
      updateButtonStates(index);
      updateStyles();
    });

    // Set initial styles and button visibility for the first button
    if (index === 0) {
      button.style.backgroundColor = "#115272";
      button.style.color = "white";
      updateButtonVisibility(index);
    }
   
    
  });




  nextBtn.addEventListener('click', switchToNextTab);
  prevBtn.addEventListener('click', switchToPrevTab);

  
  
});


document.getElementById('form').addEventListener('submit', function (event) {
  const check = document.getElementById('check').value;
  const title = document.getElementById('title').value;
  const abstract = document.getElementById('abstract').value;
  const keyword = document.getElementById('keyword').value;
  const reference = document.getElementById('reference').value;
  const notes = document.getElementById('notes').value;
  const file_name = document.getElementById('file_name').value;

  if (!check || !title || !abstract || !keyword || !reference || !notes || !file_name) {
    event.preventDefault();
    alert("Please fill in all the required fields.");
  }
});

  var quill1 = new Quill('#editor', {
    theme: 'snow'
  });
  var quill2 = new Quill('#editor2', {
    theme: 'snow'
  });
  var quill3 = new Quill('#editor3', {
    theme: 'snow'
  });

  var abstractInput = document.getElementById('abstract');
  var referenceInput = document.getElementById('reference');
  var notes = document.getElementById('notes');

  quill1.on('text-change', function() {
    abstractInput.value = quill1.getText();
  });

  quill2.on('text-change', function() {
    referenceInput = document.getElementById('reference');
  });

  quill3.on('text-change', function() {
    notes.value = quill3.getText();
  });
  


  document.getElementById('file_name').addEventListener('change', function () {
  var selectedFiles = this.files;
  var fileList = document.getElementById('fileList');

  for (var i = 0; i < selectedFiles.length; i++) {
    var fileRow = fileList.insertRow(fileList.rows.length);
    var fileNameCell = fileRow.insertCell(0);
    fileNameCell.textContent = selectedFiles[i].name;

    var fileTypeCell = fileRow.insertCell(1);
    fileTypeCell.textContent = selectedFiles[i].type;

    var actionCell = fileRow.insertCell(2);
    var deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.className = 'btn btn-danger btn-sm';
    deleteButton.textContent = 'Delete';
    deleteButton.setAttribute('data-row-index', fileRow.rowIndex);
    deleteButton.onclick = deleteFile;
    actionCell.appendChild(deleteButton);
  }
});


function openFileModal() {
  document.getElementById('file_name').click();
}

function deleteFile(rowIndex) {
  var fileList = document.getElementById('fileList');


  if (rowIndex >= 0 && rowIndex < fileList.rows.length) {
    fileList.deleteRow(rowIndex);
  }
}

document.getElementById('file_name').addEventListener('change', function (event) {
  var fileList = document.getElementById('fileList');


  while (fileList.firstChild) {
    fileList.removeChild(fileList.firstChild);
  }

  for (var i = 0; i < this.files.length; i++) {
    var file = this.files[i];

    var newRow = fileList.insertRow(i);

    newRow.insertCell(0).innerHTML = file.name;
    newRow.insertCell(1).innerHTML = file.type;

  
    var deleteButton = document.createElement('button');
    deleteButton.innerHTML = 'Delete';
    deleteButton.className = 'btn btn-danger btn-sm';
    deleteButton.addEventListener('click', function () {
      // Get the row index of the clicked delete button
      var rowIndex = this.parentElement.parentElement.rowIndex;
      deleteFile(rowIndex - 1);e 
    });

    newRow.insertCell(2).appendChild(deleteButton);
  }
});


document.getElementById('contributor-btn').addEventListener('click', function (event) {
  Swal.fire({
    html:
      '<h5 class="title9" id="title-9">Contributors</h5>' +
      '<hr id="swal-d">' +
      '<div id="fName"><label id="sub-16">First Name: </label><input id="input1" class="swal2-input"></div>' +
      '<div id= "lName"><label id="sub-17">Last Name: </label><input id="input2" class="swal2-input"></div>' +
      '<label id= "sub-18">Preferred Public Name: </label><input id="input3" class="swal2-input">' +
      '<label id= "sub-19">Email: </label><input id="input4" class="swal2-input">' +
      '<label id= "sub-20">ORCID: </label><input id="input5" class="swal2-input">',

    footer: '<button  id="confirmBtn">Add Contributor</button>',
    showConfirmButton: false,
  });

 


  

  document.getElementById('confirmBtn').addEventListener('click', function () {
  
    var input1Value = document.getElementById('input1').value;
    var input2Value = document.getElementById('input2').value;

    var contributorsValue = input1Value + ' ' + input2Value;


    var table = document.getElementById('table-contributor').getElementsByTagName('tbody')[0];
    var newRow = table.insertRow(table.rows.length);

    var cellContributor = newRow.insertCell(0);
    cellContributor.innerHTML = contributorsValue;

    var cellActions = newRow.insertCell(1);
    cellActions.innerHTML = '<button type="button" id="btn-update" class="btn btn-outline-primary btn-sm btn-update" style="margin-right: 10px;">Update</button>' +
    '<button type="button" id="btn-delete" class="btn btn-danger btn-sm btn-delete">Delete</button>';

 
    var updateBtn = cellActions.querySelector('.btn-update');
    var deleteBtn = cellActions.querySelector('.btn-delete');

    updateBtn.addEventListener('click', function (event) {
      Swal.fire({
        html: '<h5 class="title10" id="title-10">Update Contributor</h5>' +
          '<hr id="swal-d">' +
          '<div id="fName"><label id="sub-21">First Name: </label><input id="updateInput1" class="swal2-input" value="' + input1Value + '"></div>' +
          '<div id= "lName"><label id="sub-22">Last Name: </label><input id="updateInput2" class="swal2-input" value="' + input2Value + '"></div>' +
          '<label id= "sub-23">Preferred Public Name: </label><input id="updateInput3" class="swal2-input">' +
          '<label id= "sub-24">Email: </label><input id="updateInput4" class="swal2-input">' +
          '<label id= "sub-25">ORCID: </label><input id="updateInput5" class="swal2-input">',
    
        footer: '<button id="update-cont">Update</button>',
        showConfirmButton: false
      });
    
      document.getElementById('update-cont').addEventListener('click', function () {
        // Get the updated values from the modal
        var updatedInput1Value = document.getElementById('updateInput1').value;
        var updatedInput2Value = document.getElementById('updateInput2').value;
    
        // Update the displayed value in the table
        contributorsValue = updatedInput1Value + ' ' + updatedInput2Value;
        cellContributor.innerHTML = contributorsValue;
        
        // Close the update modal
        Swal.close();
      });
    });
    
    deleteBtn.addEventListener('click', function () {
    
      var rowIndex = this.closest('tr').rowIndex;
    
      table.deleteRow(rowIndex - 1); 
    
      console.log(contributorsValue);
    });

   
    Swal.close();
  });


  
});


var titleInput = document.getElementById("title");
var keywordsInput = document.getElementById("keywords")
var abstractInput = document.getElementById("abstract");
var fileInput = document.getElementById("file_name");
var contributorInput = document.getElementById("contributor").textContent;



var input5 = document.getElementById("input5");
var input6 = document.getElementById("input6");
var input7 = document.getElementById("input7");
var input8 = document.getElementById("input8");
var input9 = document.getElementById("input9");
var input10 = document.getElementById("input10");
var input11 = document.getElementById("input11");
var input12 = document.getElementById("input12");
var input13 = document.getElementById("input13");
var input14 = document.getElementById("input14");
var input15 = document.getElementById("input15");

 

titleInput.addEventListener('input', function() {
  input5.value = titleInput.value;
  input11.value = titleInput.value;
});

keywordsInput.addEventListener('input', function() {
  input6.value = keywordsInput.value;
  input12.value = titleInput.value;
});

quill1.on('text-change', function() {
  input7.value = quill1.getText();
  input13.value = quill1.getText();
});
quill2.on('text-change', function() {
  input8.value = quill2.getText();
  input14.value = quill2.getText();
});

fileInput.addEventListener('input', function() {

  if (fileInput.files.length > 0) {
    
    input9.value = fileInput.files[0].name;
    
  } else {
   
    input9.value = "";
  }
});

input10.value = contributorInput;



document.getElementById('update-cont-2').addEventListener('click', function (event) {



  Swal.fire({
    html: '<h5 class="title14" id="title-14">Update Article Details</h5>' + '<hr id="swal-d-2">' + '<label class="sub30" id="sub-30">Title: <input type="text" class="form-control" id="input11" value="'+ titleInput.value +'"></label>' +  '<label class="sub31" id="sub-31">Keywords: <input type="text" class="form-control" id="input12" value="'+ keywordsInput.value +'"></label>'  + '<label class="sub32" id="sub-32">Abstract: <input type="text" class="form-control" id="input13" id="input12" value="'+ quill1.getText() +'"></label>' +  '<label class="sub33" id="sub-33">Reference: <input type="text" class="form-control" id="input14" value="'+ quill2.getText() +'"></label>',
    footer: '<button  id="btn-article-update">Update</button>',
    showConfirmButton: false,
  });

  document.getElementById('btn-article-update').addEventListener('click', function () {
    var newTitle = document.getElementById('input11').value;
    var newKeywords = document.getElementById('input12').value;
    var newAbstract = document.getElementById('input13').value;
    var newReference = document.getElementById('input14').value;

  
    titleInput.value = newTitle;
    keywordsInput.value = newKeywords;
  
    input5.value = newTitle;
    input6.value = newKeywords;
    input7.value = newAbstract;
    input8.value = newReference;
  
    try {
      quill1.setText(newAbstract);
     
    } catch (error) {
      console.error("Unable to update Abstract Content:", error);
      
    }

    // i add two separate try catch because its not working when putting them in one :>>>
    
    try {
      quill2.setText(newReference);
     
    } catch (error) {
      console.error("Unable to update Reference Content:", error);
      
    }
    
    
  
    Swal.close();
  });
  

 
});

document.getElementById('update-cont-3').addEventListener('click', function (event) {
  
  Swal.fire({
    html: '<h5 class="title15" id="title-15">Update File Content</h5>' +  '<hr id="swal-d-3">' 
    + '<label id="sub-34">File Name: </label>' + 
    '<button type="button" class="btn btn-primary btn-sm" id="newFile">Select File</button> ' 
    + '<input type="text" class="form-control" id="input15" readonly></input>',
    showConfirmButton: false
    
  })


  document.getElementById('newFile').addEventListener('click', function() {
    document.getElementById('file_name').click();
  });

  document.getElementById('file_name').addEventListener('change', function () {
    var fileName = this.files[0].name;
    document.getElementById('input15').value = fileName;
});

});

document.getElementById('check-d').addEventListener('click', function (event){
  const formFloat = document.getElementById('form-floating-2');

  if(formFloat.style.display === "none" || formFloat.style.display === ""){
    formFloat.style.display = "inline-block";
  }else{
    formFloat.style.display = "none";
  }
  
});
 







<style>
  .label {text-transform: uppercase; font-size: .8em;}
  .wFormContainer {width: 450px; max-width: 100%;}
  .wForm .inputWrapper {width: 100% !important}
  #tfa_259-L :hover {color: blue; text-decoration: underline; cursor: pointer; tabindex}
  #tfa_259_L :focus { box-shadow: 0 0 5px 1px #08F; }
</style>

<script
  src="https://code.jquery.com/jquery-3.2.0.min.js"
  integrity="sha256-JAW99MJVpJBGcbzEuXk4Az05s/XyDdBomFqNlM3ic+I="
  crossorigin="anonymous">
</script>

<script>
  var agencyMap = {
    "ADULT FOSTER CARE": 984,
    "FAMILY CHILD CARE": 986,
    "ADDUS": 982,
    "STATE HOMECARE WORKERS": 988,
    "BAKER COUNTY EMPLOYEES": 970,
    "BASIN TRANSIT SERVICE": 977,
    "CENTRAL OREGON IRRIG DISTRICT": 992,
    "CITY OF BEAVERTON EMPLOYEES": 990,
    "CITY OF CANNON BEACH": 921,
    "CITY OF PENDLETON": 938,
    "CITY OF SPRINGFIELD EMPLOYEES": 995,
    "CITY OF TIGARD EMPLOYEES": 993,
    "CITY OF WILSONVILLE": 926,
    "COOS BAY-NORTH BEND WATER BOARD": 935,
    "CURRY COUNTY EMPLOYEES": 985,
    "JACKSON COUNTY EMPLOYEES": 925,
    "JOSEPHINE COUNTY": 951,
    "LCOG SDSD": 908,
    "LINN COUNTY EMPLOYEES ASSN": 981,
    "MARION COUNTY EMPLOYEES": 940,
    "OREGON CASCADES WEST COG": 937,
    "PORTLAND PUBLIC SCHOOL EMPLOYEES": 810,
    "THE DALLES CITY OF EMPLOYEES ASSN.": 910,
    "WALLOWA CO. ROADS DEPT.": 923,
    "WALLOWA CTY PROFESSIONAL EMPS": 924,
    "AVAMERE HEALTH SERVICES": 973,
    "EMPRES HEALTH CARE": 972,
    "EXTENDICARE HEALTH FACILITIES": 989,
    "PINNACLE CARE": 974,
    "PRESTIGE": 971,
    "EASTERN OREGON UNIVERSITY": 58010,
    "OREGON INSTITUTE OF TECHNOLOGY": 58018,
    "OREGON STATE UNIVERSITY": 58030,
    "PORTLAND STATE UNIVERSITY": 58090,
    "SOUTHERN OREGON UNIVERSITY": 58040,
    "UNIVERSITY OF OREGON": 58050,
    "WESTERN OREGON UNIVERSITY": 58020,
    "ALVORD TAYLOR": 999,
    "CASCADE AIDS PROJECT": 901,
    "CODA": 963,
    "EDUCATION NORTHWEST (FORMERLY NWREL)": 905,
    "OREGON SUPPORTED LIVING PROGRAM": 902,
    "PARRY CENTER FOR CHILDREN": 987,
    "PUBLIC BROADCASTING COMM": 57000,
    "THE CHILD CENTER": 904,
    "PPL PERSONAL SUPPORT WORKER": 150,
    "STATE PERSONAL SUPPORT WORKER": 100,
    "BOARD OF NURSING": 85100,
    "BOARD OF PHARMACY": 85500,
    "BUREAU OF LABOR & INDUSTRIES": 83900,
    "COMMISSION FOR THE BLIND": 58500,
    "COMMUNITY COLLEGE BOARD": 58600,
    "DENTISTRY BOARD": 83400,
    "DEPARTMENT OF ADMINISTRATIVE SERVICES": 10700,
    "DEPARTMENT OF AGRICULTURE": 60300,
    "DEPARTMENT OF CONSUMER & BUSINESS SERVICES": 44000,
    "DEPARTMENT OF EDUCATION": 58100,
    "DEPARTMENT OF FORESTRY": 62900,
    "DEPARTMENT OF JUSTICE": 13700,
    "DEPARTMENT OF REVENUE": 15000,
    "DEPARTMENT OF TRANSPORTATION": 73000,
    "DEPARTMENT OF TREASURY": 17000,
    "DEPARTMENT OF VETERANS AFFAIRS": 27400,
    "DHS/OHA": 10000,
    "EMPLOYMENT DIVISION": 47100,
    "HEALTH LICENSING AGENCY": 83300,
    "OFFICE OF COMM COLLEGE SERVICES": 58120,
    "OR PARKS & RECREATION DEPT": 73410,
    "OREGON DEPARTMENT OF AVIATION": 10900,
    "OREGON DEPARTMENT OF FISH AND WILDLIFE": 63500,
    "OREGON HEALTH LICENSING AGENCY": 83100,
    "OREGON HOUSING & COMMUNITY SERV": 91400,
    "OREGON MEDICAL BOARD": 84700,
    "OREGON STATE FAIR & EXPOSITION CENTER": 62200,
    "OREGON STATE LIBRARY": 54300,
    "OREGON STUDENT ASSISTANCE COMMISSION": 57500,
    "OREGON WATER SHED ENHANCEMENT BOARD": 69100,
    "OREGON YOUTH AUTHORITY": 41500,
    "PSYCHOLOGISTS EXAMINERS BOARD": 12200,
    "PUBLIC EMPLOYEES RETIREMENT SERVICES": 45900,
    "STATE BOARD OF MASSAGE THERAPISTS": 96800,
    "TEACHER STANDARDS & PRACTICES": 58400,
    "WATER RESOURCES DEPARTMENT": 69000
  };

  function getAgencyNumber(employerName) {
    return agencyMap[employerName];
  }

  $(document).ready(function(){
    // make terms display link focusable
    $("#tfa_259-L").attr("tabindex", 0);

    // hide terms text
    $("#tfa_231").hide();

    // when the employer name is selected
    $("#tfa_1").change(function() {
      var employerName = $( "#tfa_1 option:selected" ).text();
      console.log(`employerName: ${employerName}`);

      // find agencynumer based on employer name
      var agencyNumber = getAgencyNumber(employerName);
      console.log(`agencyNumber: ${agencyNumber}`);

      // set the value of the agencynumber field
      $("#tfa_2").val(agencyNumber);

    });
    // when home state is selected
    $("#tfa_42").change(function() {
      var homeState = $( "#tfa_42 option:selected" ).text();
      console.log(`homeState: ${homeState}`);

      // set the value of the mail-to state field
      $("#tfa_4").val(homeState);

    });

    // when membership terms link is clicked
    $("#tfa_259-L").click(function() {
      // show or hide membership terms
      $("#tfa_231").toggle();
    });

    // or if keypress enter while focused
    $("#tfa_259-L").keypress(function(e) {
      if(e.which == 13) {
        $("#tfa_231").toggle();
      }
    });

  });

</script>
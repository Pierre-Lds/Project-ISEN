#include <iostream>
#include <cstring>
#include "randomPopulate/studentPopulation.h"
#include "randomPopulate/staffPopulation.h"
#include "exportToCSV/exportToCSV.h"
#include "populateWithCSV/populateFromCSV.h"

using namespace std;

int main(int argc, char** argv) {

    // We check the argument to know what table we need to implement :
    //if(argc == 3){
    //    if(strcmp(argv[1], "student") == 0){
    //        // We modify the student table :
    //        studentPopulation(atoi(argv[2]));
    //    }
    //    if(strcmp(argv[1], "staff") == 0){
    //        staffPopulation(atoi(argv[2]));
    //    }
    //
    //}

    // EXPORT TO CSV :

    if(strcmp(argv[1], "populateDBWithCSV") == 0){
        populateTable();
    }

    if(argc == 3){
        if(strcmp(argv[1], "exportToCSV") == 0){
            if(strcmp(argv[2], "student") == 0){
                exportStudentToCSV();
            }
            if(strcmp(argv[2], "staff") == 0){
                exportStaffToCSV();
            }
            if(strcmp(argv[2], "thematic") == 0){
                exportThematicToCSV();
            }
            if(strcmp(argv[2], "domainePro") == 0){
                exportProffesionalDomainToCSV();
            }
            if(strcmp(argv[2], "project") == 0){
                exportProjectToCSV();
            }
        }

    }

    return 0;


}

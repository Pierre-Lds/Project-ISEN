#include <stdlib.h>
#include <iostream>
#include <mysql_connection.h>
#include <cppconn/driver.h>
#include <cppconn/exception.h>
#include <cppconn/resultset.h>
#include <cppconn/statement.h>
#include <cstring>
#include <fstream>
#include "Graph.h"
#include "GraphEcart.h"

#include "global.h"

using namespace sql;
using namespace std;

bool boolPrint = false;

int main(int argc, char** argv){

    // We have an argument to our program
    if(argc >= 2){
        // To enable the print in the console, we need : ./out a
        if(strcmp(argv[1], "true") == 0){
            boolPrint = true;
        }
        else {
            boolPrint = false;
        }
    } else {
        boolPrint = false;
    }
    if(boolPrint){
        cout << "Main : " << endl;
    }
    if(argc == 3){
        // ./out trueOrFalse fromBB
        if(strcmp(argv[2] , "fromDB") == 0){
            // To use the code from the database
            Graph* graph = new Graph();
            graph->setUpMatrix();

            //test(graph);

            graph->busackerAndGowen();

            graph->printResult();

            graph->putResultInDatabase();

            graph->clearDatabase();
        }
    }
    // ./out trueOrFalse fromCSV CSVAdress
    if(argc == 4){
        if(strcmp(argv[2], "fromCSV") == 0){
            Graph* graph = new Graph();

            graph->setUpMatrixFromCSV(argv[3]);
            graph->busackerAndGowen();

            graph->printResult();
        }
    }


    return 0;
}



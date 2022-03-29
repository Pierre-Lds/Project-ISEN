//
// Created by rouxt on 04/03/2022.
//

#ifndef RANDOMPOPULATION_POPULATEFROMCSV_H
#define RANDOMPOPULATION_POPULATEFROMCSV_H

using namespace std;


// Function to read the CSV file
std::vector<std::string> readCSVRow(const std::string &row);
std::vector<std::vector<std::string>> readCSV(std::istream &in);

void printTwoDimensionVector(vector<vector<std::string>> vect);


void populateTable();


#endif //RANDOMPOPULATION_POPULATEFROMCSV_H

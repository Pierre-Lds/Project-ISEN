//
// Created by rouxt on 04/03/2022.
//

#ifndef RANDOMPOPULATION_EXPORTTOCSV_H
#define RANDOMPOPULATION_EXPORTTOCSV_H

#include <vector>

using namespace std;

void writeCSV(string fileName, std::vector<std::pair<std::string, std::vector<int>>> dataset);


void exportStudentToCSV();
void exportStaffToCSV();
void exportThematicToCSV();
void exportProffesionalDomainToCSV();
void exportProjectToCSV();

#endif //RANDOMPOPULATION_EXPORTTOCSV_H

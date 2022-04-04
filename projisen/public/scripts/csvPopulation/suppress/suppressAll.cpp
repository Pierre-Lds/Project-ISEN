//
// Created by rouxt on 04/04/2022.
//

#include <cppconn/driver.h>
#include <cppconn/resultset.h>
#include <cppconn/statement.h>
#include "suppressAll.h"


void suppressAll(){
    sql::Driver *driver;
    sql::Statement *stmt;
    sql::ResultSet *res;
    sql::Connection *con;

    driver = get_driver_instance();
    con = driver->connect("tcp://127.0.0.1:3306", "root", "root");
    con->setSchema("projisen");

    // We delete all the tables one by one :
    stmt = con->createStatement();
    sql::SQLString req = "DELETE FROM project_wishes;";
    stmt->execute(req);

    stmt = con->createStatement();
    stmt->execute("DELETE FROM project_wishes_legacy");

    // We remove the key on the pair :
    stmt = con->createStatement();
    stmt->execute("SET FOREIGN_KEY_CHECKS = 0 ;");

    stmt = con->createStatement();
    stmt->execute("DELETE FROM student");

    stmt = con->createStatement();
    stmt->execute("DELETE FROM project");

    stmt = con->createStatement();
    stmt->execute("DELETE FROM thematic");

    stmt = con->createStatement();
    stmt->execute("DELETE FROM professional_domain");

    stmt = con->createStatement();
    stmt->execute("DELETE FROM staff");

    stmt = con->createStatement();
    stmt->execute("DELETE from project_professional_domain");

    // Put back all the keys
    stmt = con->createStatement();
    stmt->execute("SET FOREIGN_KEY_CHECKS = 1 ;");
}
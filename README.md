DbMockLibrary
==================
[![Build Status](https://travis-ci.org/ajant/DbMockLibrary.svg?branch=master)](https://travis-ci.org/ajant/DbMockLibrary)

Db mocking & dummy data management library

This is a database stubbing/mocking/prototyping library. Its principal uses are meant to be:
1. testing the application without using actual database (by mocking data persistence layer, using DbMockLibrary)

2. quick prototyping, while delaying the writing of any database specific code (again by mocking data persistence layer, using DbMockLibrary)

3. dummy data management during development phase

1. If data persistence code is kept separate from business logic code, in a different layer of the application, then data persistence layer can
be mocked using DbMockLibrary during testing. That way objects that work with data persistence layer can be tested, without actually using a
real database. As a result tests are faster and better code & test separation is achieved. DbMockLibrary could be used to mock data persistence
layer functionality in the testing environment

2. When project is in prototyping stage, often making choice on database is not necessarily needed at that time. Sometimes it's even beneficial
to postpone the decision for a while during that phase, until some features/architectural solutions take shape. What is needed is to have some
"dummy data" available, to test out features and concepts with it. DbMockLibrary could provide feature rich "dummy data" platform.

3. During development, it's often convenient to have some easy way to load/remove some "dummy data" from the database, in order to be able to
test out features, without having to create dumps from the production database. DbMockLibrary provides a simple way to manage this process for
some of the most popular databases

Requirements
============

You'll need: PHP version 5.4+

Quickstart
==========
Install the latest version with composer:<br/>
require "ajant/db-mock-library"

Auto-load the library:
use DbMockLibrary/DbMockLibrary

As of now MySQL and MongoDb databases have been implemented.
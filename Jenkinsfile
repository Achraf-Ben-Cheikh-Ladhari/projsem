pipeline {
  agent any
  environment {
    NEW_VERSION = '1.3.0'
    SERVER_CREDENTIALS = credentials('')
  }
  parameters {
    choice(name: 'VERSION', choices: ['1.1.0', '1.2.0', '1.3.0'], description: '')
    booleanParam(name: 'executeTests', defaultValue: true, description: '')
  }
  stages {
    stage("build") {
      steps {
        echo 'Building the application...'
        echo "Building version ${NEW_VERSION}"
      }
    }

    stage("test"){
      when{
        expression{
          BRANCH_NAME == 'dev' || BRANCH_NAME == 'test'
        }
      }
      steps{
        echo 'Testing the application...'
      }
    }

    stage("deploy"){
      steps{
        echo 'Deploy the application...'
        withCredentials([
          usernamePassword(credentials: 'server-credentials',usernameVariable: USER, passwordVariable:PWD)
        ]){
          sh "PWD ${USER} ${PWD}"
        }
        echo "Building version ${SERVER_CREDENTIALS}"
        echo "Building version ${params.VERSION}"
        
      }
    }
    
  }

}
